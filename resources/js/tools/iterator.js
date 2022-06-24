class ArrayIterator
{
    static make(data) {
        let index = 0;
        return {
            next() {
                if (index < data.length) { 
                    return {value: data[index++], done: false};
                } else {
                    index = 0;
                    return {done: true};
                }
            },
            forward(step = 1) {
                index += step;
            },
            backward(step = 1) {
                index -= step;
                if (index < 0) {
                    index = 0;
                }
            },
            rewind() {
                index = 0;
            },
        };
    }
}

class TreeNode
{
    constructor(data, leafs = null, root = null) {
        this._data = data; 
        this._leafs = leafs;
        this._root = root;
    }
    
    get data() {
        return this._data;
    }
    
    get leafs() {
        return this._leafs;
    }
    
    set leafs(value) {
        this._leafs = value;
        return value;
    }
    
    get root() {
        return this._root;
    }
    
    set root(value) {
        this._root = value;
        return value;
    }
    
    get isTerminal() {
        return !this._leafs || this._leafs.length === 0;
    }
    
    getLeaf(index) {
        if (! this.isTerminal) {
            if (index >= 0 && index < this._leafs.length) {
                return this._leafs[index];
            }
            return this._leafs[0];
        }
        return null;
    }
}

class List
{
    constructor(items) {
        this._iterator = ArrayIterator.make(items);
        this.next();
    }
    
    next() {
        this._current = this._iterator.next();
    }
    
    get current() {
        return this._current.value;
    }
    
    get hasMore() {
        return !this._current.done;
    }
    
    backward(step = 1) {
        this._iterator.backward(step + 1);
        this.next();
    }
    
    rewind() {
        this._iterator.rewind();
        this.next();
    }
}

class Tree
{
    constructor(root) {
        this._root = new TreeNode(null, [root]);
        this._current = this._root;
        root.root = this._root;
    }
    
    next(leaf = 0) {
        this._current = this._current.getLeaf(leaf);
    }
    
    get current() {
        if (this._current.isTerminal) {
            return undefined;
        }
        if (this._current.leafs.length === 1) {
            return this._current.leafs[0].data;
        }
        return this._current.leafs.map((leaf) => leaf.data);
    }
    
    get hasMore() {
        return !this._current.isTerminal;
    }
    
    backward(step = 1) {
        while (this._current && step > 0) {
            if (!this._current.root) {
                break;
            }
            step--;
            this._current = this._current.root;
        }
    }
    
    rewind() {
        this._current = this._root;
    }
}

export {ArrayIterator, TreeNode, List, Tree};