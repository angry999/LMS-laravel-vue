import CatalogueUnit from './catalogue-unit';

class Category
{
    constructor(catalogue, data) {
        this._catalogue = catalogue;
        this._data = data;
        this._units = data.units
            .sort((a, b) => a.sort_position - b.sort_position)
            .map((item) => new CatalogueUnit(catalogue, item));
    }
    
    get name() {
        return this._data.name;
    }
    
    get slug() {
        return lodash.words(this._data.name.toLowerCase()).join('-');
    }
    
    get title() {
        return this._data.display_name;
    }
    
    get color() {
        return this._data.color;
    }
    
    get icon() {
        return fix_protocol(this._data.image_url);
    }
    
    get description() {
        return this._data.description;
    }
    
    get units() {
        return this._units;
    }
    
    get startLevel() {
        return this._data.start_level;
    }
    
    get endLevel() {
        return this._data.end_level;
    }
    
    get progress() {
        return this._catalogue.getProgress().getCategoryProgress(this);
    }
    
    get size() {
        return this._units.length;
    }
    
    getUnit(id) {
        return lodash.find(this._units, (unit) => unit.id === id);
    }
}

export default Category;