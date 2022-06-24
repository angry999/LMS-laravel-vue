class QuizReport
{
    constructor(unit, results) {
        this._unit = unit;
        this._results = results;
    }
    
    submit() {
        return axios.post('/api/unit/' + this._unit.id + '/quiz-report', {
            results: this._results,
        }).then((response) => {
            return Promise.resolve(response.data.progress);
        });
    }
}

export default QuizReport;