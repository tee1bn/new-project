class Delay {


    constructor() {
        this.delay = 10;
        this.target_action_completed = false;
        this.target_action = null;
        this.target_response = null;
        this.start_time = null;
        this.pending_action = null;

    }

    setTargetAction(target_action, params) {
        this.target_action = new Promise((resolve, reject) => {
            resolve(target_action(...params))
        });

        return this;
    }

    setDelay(delay) {
        this.delay = delay;
        return this;
    }
    setPendingAction(pending_action) {
        this.pending_action = pending_action;
        return this;
    }


    checkTime() {

        let _timing = setInterval(() => {
            var end_time = Math.round((new Date).getTime() / 1000)
            var used_time = end_time - this.start_time;
            console.log(used_time, this.target_action_completed)

            if ((used_time) >= this.delay && (this.target_action_completed == true)) {
                this.pending_action.stop();

                clearInterval(_timing);
            }
        }, 1000);

    }

    notifyCompletion() {
        this.target_action_completed = true;
    }

    run() {
        return new Promise((resolve, reject) => {

            this.checkTime();

            this.start_time = Math.round((new Date).getTime() / 1000)
            setTimeout(() => {
                this.pending_action.start();
            }, 500);


            this.target_action.then((val) => {
                this.target_response = val;
                this.notifyCompletion()
                resolve(val)
            })

        })
    }
}

class Ad {
    start() {

        this.state = true;
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        })

    }

    stop() {
        this.state = false;
        $("#myModal").modal('hide');
    }
}