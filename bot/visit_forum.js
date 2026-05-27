const puppeteer = require('puppeteer-core');

const WEB_URL = process.env.WEB_URL || 'http://web:80';
const INTERVAL_MS = 10 * 1000;

async function visitForum() {
    const browser = await puppeteer.launch({
        executablePath: '/usr/bin/chromium',
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'],
    });

    try {
        const page = await browser.newPage();

        await page.goto(WEB_URL + '/login.php', { waitUntil: 'networkidle0' });
        await page.type('input[name="username"]', 'anghel.cristi');
        await page.type('input[name="password"]', 'password');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]'),
        ]);

        await page.goto(WEB_URL + '/admin/forum.php', { waitUntil: 'networkidle0' });

        await page.evaluate(() => {
            document.querySelectorAll('a').forEach(a => {
                if (a.href && !a.href.startsWith('javascript:')) {
                    window.open(a.href, '_blank');
                }
            });
        });

        await new Promise(r => setTimeout(r, 2000));

        const ts = new Date().toISOString();
        console.log(`[${ts}] Admin visited forum successfully`);
    } catch (e) {
        console.error(`[BOT] Error: ${e.message}`);
    } finally {
        await browser.close();
    }
}

async function main() {
    console.log(`[BOT] Starting admin forum visitor with Puppeteer...`);
    console.log(`[BOT] Visiting: ${WEB_URL}/admin/forum.php`);
    console.log(`[BOT] Interval: ${INTERVAL_MS / 1000}s\n`);

    while (true) {
        await visitForum();
        await new Promise(r => setTimeout(r, INTERVAL_MS));
    }
}

main().catch(e => {
    console.error('[BOT] Fatal:', e);
    process.exit(1);
});
