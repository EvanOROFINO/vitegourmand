/**
 * Script de capture de maquettes haute fidélité — Vite & Gourmand.
 * Utilise Puppeteer pour prendre des screenshots desktop et mobile des pages clés.
 *
 * Usage : node tools/screenshots.cjs [baseUrl]
 */
const path = require('path');
const fs = require('fs');

const baseUrl = process.argv[2] || 'http://localhost:8001';
const outDir = path.join(__dirname, '..', 'docs', 'maquettes', 'img');
if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

const credentials = {
  email: 'admin@vitegourmand.fr',
  password: 'Password123!',
};

const pages = [
  { url: '/',          name: '01-accueil',            auth: false },
  { url: '/menus',     name: '02-liste-menus',        auth: false },
  { url: '/menu/1',    name: '03-detail-menu',        auth: false },
  { url: '/login',     name: '04-connexion',          auth: false },
  { url: '/register',  name: '05-inscription',        auth: false },
  { url: '/employe',   name: '06-employe-dashboard',  auth: true  },
];

const viewports = {
  desktop: { width: 1440, height: 900, isMobile: false },
  mobile:  { width: 414, height: 896,  isMobile: true, hasTouch: true, deviceScaleFactor: 2 },
};

(async () => {
  const puppeteer = require('puppeteer');
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  for (const [device, viewport] of Object.entries(viewports)) {
    console.log(`\n=== Captures ${device} (${viewport.width}x${viewport.height}) ===`);
    const page = await browser.newPage();
    await page.setViewport(viewport);

    if (pages.some(p => p.auth)) {
      try {
        await page.goto(baseUrl + '/login', { waitUntil: 'networkidle2', timeout: 15000 });
        // Fermer le menu mobile si présent
        if (device === 'mobile') {
          const toggle = await page.$('.navbar-toggler');
          if (toggle) await toggle.click();
        }
        await page.waitForSelector('input[name="email"]', { timeout: 5000 });
        await page.type('input[name="email"]', credentials.email);
        await page.type('input[name="password"]', credentials.password);
        await Promise.all([
          page.click('button[type="submit"]'),
          page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 15000 }),
        ]);
        console.log('  Login OK');
      } catch (e) {
        console.log('  Login skip : ' + e.message);
      }
    }

    for (const p of pages) {
      const filename = `${p.name}-${device}.png`;
      const filepath = path.join(outDir, filename);
      try {
        await page.goto(baseUrl + p.url, { waitUntil: 'networkidle2', timeout: 15000 });
        await new Promise(r => setTimeout(r, 600));
        await page.screenshot({ path: filepath, fullPage: false });
        console.log(`  ${filename}`);
      } catch (e) {
        console.log(`  ECHEC ${filename} : ${e.message}`);
      }
    }
    await page.close();
  }

  await browser.close();
  console.log('\nTermine. Images dans ' + outDir);
})().catch(e => {
  console.error('Erreur fatale :', e.message);
  process.exit(1);
});
