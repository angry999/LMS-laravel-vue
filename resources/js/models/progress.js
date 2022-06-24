class ActivityProgress
{
    constructor(data) {
        this._data = data;
    }
    
    get number() {
        return this._data.activity_number;
    }
    
    get score() {
        return this._data.best_score;
    }
}

class UnitProgress
{
    constructor(data) {
        this._data = data;
        this._activities = [];
        if (data.activities) {
            this._activities = data.activities.map((item) => new ActivityProgress(item));
        }
    }
    
    get unit() {
        return this._data.unit;
    }
    
    get score() {
        return this._data.best_score;
    }
    
    get completed() {
        return this._data.activities_completed;
    }
    
    get total() {
        return this._data.activities_total;
    }
    
    get isCompleted() {
        return !!this._data.completed_at;
    }
    
    get progress() {
        return this.total > 0 ? Math.round(100 * this.completed / this.total) : 0;
    }
    
    get activities() {
        return this._activities;
    }
    
    getActivityProgress(number, getter = null, default_value = null) {
        let activity = this._activities.find((item) => item.number === number);
        if (getter === null) {
            return activity;
        }
        if (activity) {
            return getter(activity);
        }
        return default_value;
    }
}

class Progress
{
    constructor(data) {
        this._data = data;
        this._per_unit = {};
        data.per_unit.forEach((item) => {
            let progress = new UnitProgress(item);
            this._per_unit[progress.unit] = progress;
        });
    }
    
    update(unit, value) {
        let progress = new UnitProgress(value);
        this._per_unit[progress.unit] = progress;
        this._data.per_unit = this._data.per_unit.map((item) => item.unit === unit.id ? value : item);
    }
    
    getCategoryProgress(category) {
        let total = 0;
        let completed = 0;
        category.units.forEach((unit) => {
            let progress = this.getUnitProgress(unit);
            if (progress && progress.isCompleted) {
                completed++;
            }
            total++;
        });
        return total > 0 ? Math.round(100 * completed / total) : 0;
    }
    
    getUnitProgress(unit, getter = null, default_value = null) {
        if (getter === null) {
            return this._per_unit[unit.id];
        }
        if (unit.id in this._per_unit) {
            return getter(this._per_unit[unit.id]);
        }
        return default_value;
    }
}

export {Progress, UnitProgress};