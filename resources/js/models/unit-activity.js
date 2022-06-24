import { ArrayIterator, TreeNode, List, Tree } from '../tools/iterator';

const SPEAKER = 1;
const READER = 2;
const SPEECHER = 3;
const GRAMMAR_FILTER = new RegExp('[\\s.,;!?-]+', 'g');
const READING = 'reading';
const SPEECH = 'speech';
const QUIZ = 'quiz';
const DOUBLE_ACTIVITY_NUMBER = 8;
const BRANCHING_RULES = {
    1: [0, 0, 0, 0],
    2: [1, 0, 0, 0],
    3: [0, 1, 0, 0],
    4: [1, 1, 0, 0],
    5: [0, 0, 1, 0],
    6: [1, 0, 1, 0],
    7: [0, 1, 1, 0],
};

class Cue {
    constructor(unit, actor, data) {
        this._unit = unit;
        this._actor = actor;
        this._data = data;
        this._result = null;
        this._recordingId = null;
    }

    get unit() {
        return this._unit;
    }

    get actor() {
        return this._actor;
    }

    get isSpeaker() {
        return this._actor == SPEAKER;
    }

    get isReader() {
        return this._actor == READER;
    }

    get isSpeech() {
        return this._actor == SPEECHER;
    }

    get guide() {
        return this._data.guide ? this._unit.getContentUrl(this._data.guide) : null;
    }

    get text() {
        return this._data.text;
    }

    get sentenceId() {
        return this._data.sentence_id;
    }

    get nodeId() {
        return this._data.node_id;
    }

    get startSnapshot() {
        return this._data.start_snapshot ? this._unit.getContentUrl(this._data.start_snapshot) : null;
    }

    get endSnapshot() {
        return this._data.end_snapshot ? this._unit.getContentUrl(this._data.end_snapshot) : null;
    }

    get video() {
        return this._data.video ? this._unit.getContentUrl(this._data.video) : null;
    }

    get example_answer_video() {
        return this._data.example_answer_video ? this._unit.getContentUrl(this._data.example_answer_video) : null;
    }

    get audio() {
        return this._data.audio ? this._unit.getContentUrl(this._data.audio) : null;
    }

    get grammar() {
        return '[' + this.baseGrammar + ']';
    }

    get baseGrammar() {
        return '(' + lodash.trim(this._data.text.replace(GRAMMAR_FILTER, ' ')).toLowerCase() + ')';
    }

    get result() {
        return this._result;
    }

    set result(value) {
        this._result = value;
        value.sentence = this;
        return value;
    }

    get recordingId() {
        return this._recordingId;
    }

    set recordingId(value) {
        this._recordingId = value;
        return value;
    }

    set setActor(val) {
        this._actor = val;
    }

    generateRecordingId(user) {
        this._recordingId = [
            user.id,
            this._unit.id,
            this.sentenceId,
            Date.now(),
        ].join('_');
    }
}

class MultiCue {
    constructor(cues) {
        this._cues = cues;
        this._recordingId = null;
    }

    get cues() {
        return this._cues;
    }

    generateRecordingId(user) {
        let first = this._cues[0];
        first.generateRecordingId(user);
        for (let cue of this._cues) {
            cue.recordingId = first.recordingId;
        }
        this._recordingId = first.recordingId;
    }

    get recordingId() {
        return this._recordingId;
    }

    get grammar() {
        return '[' + this._cues.map((cue) => cue.baseGrammar).join('') + ']';
    }

    get text() {
        return this._cues.map((cue) => cue.text);
    }

    findCue(text) {
        let gramm = '(' + text + ')';
        for (let i = 0; i < this._cues.length; i++) {
            if (gramm === this._cues[i].baseGrammar) {
                return i;
            }
        }
        return -1;
    }

    getCue(index = 0) {
        return this._cues[index];
    }
}

class QuizQuestion {
    constructor(data) {
        let index = 0;
        this._data = data;
        this._answers = data.answers.map((answer) => {
            return new QuizAnswer(answer, index++);
        });
    }

    shuffle() {
        this._answers = lodash.shuffle(this._answers);
        return this;
    }

    get id() {
        return this._data.id;
    }

    get text() {
        return this._data.question;
    }

    get answers() {
        return this._answers;
    }
}

class QuizAnswer {
    constructor(text, index) {
        this._text = text;
        this._index = index;
    }

    get text() {
        return this._text;
    }

    get isCorrect() {
        return this._index === 0;
    }

    get id() {
        return this._index;
    }
}

class Activity {
    constructor(unit, number) {
        this._unit = unit;
        this._number = number;
    }

    get unit() {
        return this._unit;
    }

    get number() {
        return this._number;
    }
}

class Reading extends Activity {
    constructor(unit, number) {
        super(unit, number);
        this._iterations = null;
        this._all = null;
    }

    createIterations() {
        if (this.isDouble) {
            return this.createTreeIterations();
        } else {
            return this.createLinearIterations();
        }
    }

    createLinearIterations() {
        let iterations = [];
        let node = this._unit.getDialog(this._number - 1);
        let iteration = 0;
        do {
            iterations.push(new Cue(this._unit, SPEAKER, node));
            node = this.pickOption(node, iteration++);
            if (node) {
                iterations.push(new Cue(this._unit, READER, node));
                node = node.next;
            }
        } while (node);
        this._all = iterations;
        return iterations;
    }

    createTreeIterations() {
        this._all = [];
        return this.createBranch(this._unit.branchingTree);
    }

    createBranch(node, root = null) {
        let cue = new Cue(this._unit, SPEAKER, node);
        let treeNode = new TreeNode(cue);
        treeNode.root = root;
        this._all.push(cue);
        if (node.options.length) {
            let options = [];
            for (let opt of node.options) {
                let cue = new Cue(this._unit, READER, opt);
                let optNode = new TreeNode(cue);
                optNode.root = treeNode;
                this._all.push(cue);
                if (opt.next) {
                    optNode.leafs = [this.createBranch(opt.next, optNode)];
                }
                options.push(optNode);
            }
            treeNode.leafs = options;
        }
        return treeNode;
    }

    pickOption(node, iteration) {
        if (node.options.length) {
            let rule = BRANCHING_RULES[this._number];
            if (rule === undefined || rule.length <= iteration || node.options.length <= rule[iteration]) {
                return node.options[0];
            }
            return node.options[rule[iteration]];
        }
        return null;
    }

    makeIterator() {
        if (this.isDouble) {
            return new Tree(this.iterations);
        } else {
            return new List(this.iterations);
        }
    }

    get score() {
        return this._unit.getActivityScore(this.number);
    }

    get iterations() {
        if (this._iterations === null) {
            this._iterations = this.createIterations();
        }
        return this._iterations;
    }

    get activityType() {
        return READING;
    }

    get locked() {
        return this._number > this._unit.activitiesCompleted + 1;
    }

    get completed() {
        return this._number < this._unit.activitiesCompleted + 1;
    }

    get isDouble() {
        return this._unit.isBranching && this._number === DOUBLE_ACTIVITY_NUMBER;
    }

    get all() {
        return this._all;
    }
}

class Quiz extends Activity {
    constructor(unit, number) {
        super(unit, number);
        this._questions = null;
    }

    createQuestions() {
        return this._unit.quiz.map((question) => {
            return new QuizQuestion(question);
        });
    }

    getQuestion(index) {
        return this.questions[index];
    }

    get activityType() {
        return QUIZ;
    }

    get questions() {
        if (this._questions === null) {
            this._questions = this.createQuestions();
        }
        return this._questions;
    }

    get locked() {
        return this._unit.activitiesCompleted < this._unit.activitiesTotal;
    }

    get completed() {
        return this._unit.quizPassed;
    }
}

class Speech extends Activity {
    constructor(unit, number) {
        super(unit, number);
        this._iterations = null;
        this._all = null;
    }

    createIterations() {
        if (this.isDouble) {
            return this.createTreeIterations();
        } else {
            return this.createLinearIterations();
        }
    }

    // access here
    createLinearIterations() {
        let iterations = [];
        let node = this._unit.getSpeech(this._number - 1 - this._unit.activitiesTotal);

        let iteration = 0;

        do {
            iterations.push(new Cue(this._unit, SPEAKER, node[iteration]));
            iteration++;
        } while (node.length > iteration);

        this._all = iterations;
        return iterations;
    }

    createTreeIterations() {
        this._all = [];
        return this.createBranch(this._unit.branchingTree);
    }

    createBranch(node, root = null) {
        let cue = new Cue(this._unit, SPEAKER, node);
        let treeNode = new TreeNode(cue);
        treeNode.root = root;
        this._all.push(cue);
        if (node.length) {
            let options = [];
            for (let opt of node) {
                let cue = new Cue(this._unit, SPEAKER, opt);
                let optNode = new TreeNode(cue);
                optNode.root = treeNode;
                this._all.push(cue);
                if (opt.next) {
                    optNode.leafs = [this.createBranch(opt.next, optNode)];
                }
                options.push(optNode);
            }
            treeNode.leafs = options;
        }
        return treeNode;
    }

    pickOption(node, iteration) {
        if (node.length) {
            return node[iteration];
        }
        return null;
    }

    makeIterator() {
        if (this.isDouble) {
            return new Tree(this.iterations);
        } else {
            return new List(this.iterations);
        }
    }

    get score() {
        return this._unit.getActivityScore(this.number);
    }

    get iterations() {
        if (this._iterations === null) {
            this._iterations = this.createIterations();
        }
        return this._iterations;
    }

    get activityType() {
        return SPEECH;
    }

    get locked() {
        return this._number > this._unit.activitiesCompleted + 1;
    }

    get completed() {
        return this._number < this._unit.activitiesCompleted + 1;
    }

    get isDouble() {
        return this._unit.isBranching && this._number === DOUBLE_ACTIVITY_NUMBER;
    }

    get all() {
        return this._all;
    }
}

export { Reading, Speech, Quiz, Cue, MultiCue, READING, QUIZ, SPEECH };