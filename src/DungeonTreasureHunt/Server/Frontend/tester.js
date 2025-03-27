class Tester {
    tests = {};

    registerTest(description, testFn) {
        this.tests[description] = testFn;
    }

    async runTests() {
        const testDescriptions = Object.keys(this.tests);
        console.log(testDescriptions);

        return Promise.all(
            testDescriptions.map(key => {
                let testFn = this.tests[key];
                if (testFn[Symbol.toStringTag] !== 'AsyncFunction') {
                    testFn = () => new Promise((resolve, reject) => {
                        try {
                            testFn();
                            resolve();
                        } catch (error) {
                            reject(error);
                        }
                    });
                }
                let promise = testFn();
                return promise.then(
                    () => {
                        console.log('Test ' + key + ' passed!');
                    }
                ).catch(error => {
                    console.error('Test ' + key + ' failed ' + error);
                });
            })
        );

    }
}

document.Tester = new Tester();