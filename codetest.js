let speed = 3000; // 1 second
let counter = 0;

function timeout() {
    setTimeout(function () {
        console.log('hi ' + counter++);
    }, speed);
}

timeout();
