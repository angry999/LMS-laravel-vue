class ReadingReport 
{
    constructor(unit, number) {
        this._unit = unit;
        this._number = number;
        this._results = [];
    }
    
    addResult(nodeId, sentenceId, score) {
        this._results.push({
            sentence_id: sentenceId,
            node_id: nodeId,
            grade: score,
        });
        return this;
    }
    
    submit() {
        return axios.post('/api/unit/' + this._unit.id + '/reading-report', {
            number: this._number,
            results: this._results,
        }).then((response) => {
            return Promise.resolve(response.data.progress);
        });
    }
}

export default ReadingReport;