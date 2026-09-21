"use strict";
const fs = require("fs");
const { execFileSync } = require("child_process");
const path = require("path");

const G = "C:\\Users\\deepak studio\\Documents\\Default Project\\deepakstudios";
const IDX = path.join(G, "index.html");

function readLines(p) { return fs.readFileSync(p, "utf8").split(/\r?\n/); }
function writeLines(p, lines) { fs.writeFileSync(p, lines.join("\n"), "utf8"); }

function gitShow(ref) {
  return execFileSync("git", ["-C", G, "show", ref + ":index.html"], { encoding: "utf8" });
}
function gitRun(args, allowFail) {
  try {
    return execFileSync("git", ["-C", G].concat(args), { encoding: "utf8" });
  } catch (e) {
    if (allowFail) return e.stdout ? e.stdout : "";
    throw e;
  }
}

function findNavSeam(lines, openRe) {
  let s = -1;
  for (let i = 0; i < lines.length; i++) {
    if (openRe.test(lines[i])) { s = i; break; }
  }
  if (s < 0) throw new Error("open seam missing: " + openRe);
  let depth = 0;
  for (let i = s; i < lines.length; i++) {
    depth += (lines[i].match(/<nav\b/g) || []).length;
    depth -= (lines[i].match(/<\/nav>/g) || []).length;
    if (depth === 0 && i > s) return [s, i];
  }
  throw new Error("close seam missing after " + openRe);
}

function findDivSeam(lines, openRe) {
  let s = -1;
  for (let i = 0; i < lines.length; i++) {
    if (openRe.test(lines[i])) { s = i; break; }
  }
  if (s < 0) throw new Error("open div seam missing: " + openRe);
  let depth = 0;
  for (let i = s; i < lines.length; i++) {
    depth += (lines[i].match(/<div\b/g) || []).length;
    depth -= (lines[i].match(/<\/div>/g) || []).length;
    if (depth === 0 && i > s) return [s, i];
  }
  throw new Error("close div seam missing after " + openRe);
}

function show(title, ln, s, e) {
  console.log("=== " + title + " : lines " + (s + 1) + ".." + (e + 1) + " ===");
  for (let i = s; i <= e; i++) console.log("    | " + ln[i].trim());
}

const cur = readLines(IDX);
const old = gitShow("e9240fb").split(/\r?\n/);

const oldDesk = findNavSeam(old, /^<nav class="desk">/);
show("(1) OLD e9240fb 5-item DESK nav (swap-target)", old, oldDesk[0], oldDesk[1]);

const oldMob = findDivSeam(old, /<div id="mobmenu">/);
show("(2) OLD e9240fb 5-item MOB nav (swap-target)", old, oldMob[0], oldMob[1]);

const curDesk = findNavSeam(cur, /^<nav class="desk">/);
show("(3) CURRENT 7-item DESK nav (isse replace)", cur, curDesk[0], curDesk[1]);

const curMob = findDivSeam(cur, /<div id="mobmenu">/);
show("(4) CURRENT 7-item MOB nav (isse replace)", cur, curMob[0], curMob[1]);

const curDeskText = cur.slice(curDesk[0], curDesk[1] + 1).join("\n");
const curMobText = cur.slice(curMob[0], curMob[1] + 1).join("\n");
const oldDeskText = old.slice(oldDesk[0], oldDesk[1] + 1).join("\n");
const oldMobText = old.slice(oldMob[0], oldMob[1] + 1).join("\n");

if (curDeskText.indexOf("Cinematography") < 0) throw new Error("current desk me Cinematography nahi - abort");
if (curMobText.indexOf("Cinematography") < 0) throw new Error("current mob me Cinematography nahi - abort");
if (oldDeskText.indexOf("Cinematography") >= 0) throw new Error("OLD desk me Cinematography - e9240fb 5-item nahi? abort");
if (oldMobText.indexOf("Cinematography") >= 0) throw new Error("OLD mob me Cinematography - e9240fb 5-item nahi? abort");

let out = cur.slice();
out.splice(curDesk[0], curDesk[1] - curDesk[0] + 1, ...old.slice(oldDesk[0], oldDesk[1] + 1));
const mob2 = findDivSeam(out, /<div id="mobmenu">/);
out.splice(mob2[0], mob2[1] - mob2[0] + 1, ...old.slice(oldMob[0], oldMob[1] + 1));

writeLines(IDX, out);
console.log("   index.html written: desk+mob swapped to old 5-item (navbar-revert)");

for (const f of ["cinematography.php", "about.php"]) {
  const p = path.join(G, f);
  if (fs.existsSync(p)) { fs.unlinkSync(p); console.log("   removed: " + f); }
}

const chk = readLines(IDX);
const allTxt = chk.join("\n");
console.log("=== (5) POST-SWAP VERIFY ===");
console.log("   Cinematography-hits-in-index = " + (allTxt.split("Cinematography").length - 1));
console.log("   desk-item-links:");
const d2 = findNavSeam(chk, /^<nav class="desk">/);
for (let i = d2[0]; i <= d2[1]; i++) {
  const t = chk[i].trim();
  if (t.indexOf("href=") >= 0) console.log("     d| " + t);
}

gitRun(["add", "-A"]);
const commitMsg = "revert navbar: restore 5-item desk+mob nav from e9240fb (Home Photography Cinematography Shorts / Reels Contact + Call Now), remove cinematography.php & about.php (7-item nav ke pages); hero/services/gallery/reviews/contact untouched by this revert";
console.log(gitRun(["commit", "-m", commitMsg]));
console.log(gitRun(["push", "origin", "main"]));
console.log("=== git log: ===");
console.log(gitRun(["log", "--oneline", "-3"]));
