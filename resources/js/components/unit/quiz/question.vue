<template>
    <div class="quiz-question">
        <div class="text">{{ question.text }}</div>
        <div class="answers">
            <div 
                v-for="(answer, index) in question.answers"
                :class="answerClass(answer)"
                class="answer"
                @click="select(answer)">
                <span class="icon-32">{{ index + 1 }}</span>
                {{ answer.text }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        question: Object,
    },
    data() {
        return {
            selected: null,
            revealed: false,
        };
    },
    methods: {
        answerClass(answer) {
            if (this.revealed) {
                return answer.isCorrect ? 'correct' : 'incorrect';
            }
            if (this.selected !== null) {
                if (answer.isCorrect) {
                    return 'correct';
                }
                if (this.selected === answer.id) {
                    return 'incorrect';
                }
            }
            return 'pending';
        },
        select(answer) {
            if (this.selected === null && !this.revealed) {
                this.selected = answer.id;
                this.$emit('answer', answer);
            }
        },
        reveal() {
            this.revealed = true;
        },
    },
};
</script>