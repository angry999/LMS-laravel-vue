class QueueItem
{
    constructor(url) {
        this.img = new Image();
        this.img.src = url;
    }
    
    ready(callback) {
        if (this.img.complete) {
            callback(this.img);
        } else {
            this.img.onerror = this.img.onload = () => {
                callback(this.img);
            }
        }
    }
}

class ImageLoader
{
    constructor(urls = []) {
        this.queue = [];
        this.done = 0;
        this.oncompleted = null;
        this.onprogress = null;
        for (let url of urls) {
            this.enqueue(url);
        }
    }
    
    enqueue(url) {
        let item = new QueueItem(url);
        this.queue.push(item);
        item.ready((img) => {
            this.loaded(img);
        });
    }
    
    loaded(img) {
        this.done++;
        if (this.onprogress) {
            this.onprogress(img, this.done, this.queue.length);
        }
        if (this.done >= this.queue.length && this.oncompleted) {
            this.oncompleted(this.queue);
        }
    }
    
    progress(callback) {
        this.onprogress = callback;
    }
    
    completed(callback) {
        if (this.done >= this.queue.length) {
            callback(this.queue);
        } else {
            this.oncompleted = callback;
        }
    }
    
    reset() {
        this.queue = [];
        this.done = 0;
        this.oncompleted = null;
    }
}

export default ImageLoader;