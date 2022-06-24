import Category from '../models/category';
import {Progress} from '../models/progress';

class Catalogue
{
    constructor() {
        this._categories = null;
        this._progress = null;
    }
    
    load() {
        return axios.get('/api/catalogue').then((response) => {
            this._categories = response.data.catalogue.categories.map((item) => new Category(this, item));
            this._progress = new Progress(response.data.progress);
            return this._categories;
        });
    }
    
    getCategories() {
        if (this._categories === null) {
            return this.load();
        } else {
            return Promise.resolve(this._categories);
        }
    }
    
    getCategory(name) {
        return this.getCategories().then((categories) => {
            return lodash.find(categories, (category) => category.slug === name);
        });
    }
    
    getProgress() {
        return this._progress;
    }
    
    updateProgress(unit, value) {
        if (this._progress) {
            this._progress.update(unit, value);
        }
    }
    
    getTotalSize() {
        return this.getCategories().then((categories) => {
            let total = 0;
            categories.forEach((category) => {
                total += category.size;
            });
            return Promise.resolve(total);
        });
    }
    
    fresh() {
        this._categories = null;
        this._progress = null;
    }
}

export default new Catalogue();