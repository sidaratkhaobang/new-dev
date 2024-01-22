
window.screenshot = {
    fullPageScreenshot: async function (filename) {
        await new Promise((resolve) => {
            const scrollHeight = document.documentElement.scrollHeight;
            const viewportHeight = window.innerHeight;

            let offset = 0;
            const chunks = Math.ceil(scrollHeight / viewportHeight);

            const interval = setInterval(() => {
                window.scrollTo(0, offset);

                offset += viewportHeight;

                if (offset >= scrollHeight) {
                    clearInterval(interval);
                    resolve();
                }
            }, 500);
        });

        await this.screenshot(filename);
    },
};
