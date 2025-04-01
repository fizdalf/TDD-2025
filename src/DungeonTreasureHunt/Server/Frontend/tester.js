class Tester {
    tests = {};

    registerTest(description, testFn) {
        this.tests[description] = testFn;
    }

    async runTests(pattern = ".*") {
        const regex = new RegExp(pattern);
        const testDescriptions = Object.keys(this.tests).filter(desc => regex.test(desc));
        console.log(testDescriptions);

        return Promise.all(
            testDescriptions.map(key => {
                let testFn = this.tests[key];
                if (testFn[Symbol.toStringTag] !== 'AsyncFunction') {
                    const originalFunction = testFn;
                    testFn = () => new Promise((resolve, reject) => {
                        try {
                            originalFunction();
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
