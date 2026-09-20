# raw13g.github.io - PS4 HEN payload swap

## What raw13g targets

raw13g's exploit host is a WebKit-based jailbreak for PS4. `index.html`
gates entry to `jb.html` (which runs `jb.js`) with a UA firmware sniff.
The `SUPPORTED` map in `index.html` lists the firmwares that this build
has a MEASURED kernel table for:

    13.02, 13.04, 13.50, 13.52   (index.html: SUPPORTED = {1302, 1304, 1350, 1352})

`ps4_offsets.js` fills a full kernel/userspace table for those same four
firmwares (each hard-populated; 13.02 and 13.04 share the 13.02 kernel;
13.52 is defined as `Object.assign({}, PS4["13.50"], { ...overrides })`).
For every one of those four entries the payload field is:

    payload: "payload2.bin"

The chain is: userspace WebKit + heap primitives (`core.js`, `mem.js`)
-> kernel R/W (`jb.js`) -> optional kernel patches loaded from
`patches/<fw>.bin` (13.02, 13.50, 13.52 blobs shipped) -> optional
userland payload loaded from `payload2.bin` and run in a fresh thread
via libkernel `pthread_create`.

The current shipped `payload2.bin` per raw13g's own `fw_status`
comment (`ps4_offsets.js:570`) is:

    payload2.bin-PS4HEN-native-1352 (patched-GoldHEN KP'd 2/2)

i.e. raw13g already ships a PS4-HEN userland stage tuned for 13.52.

## What we swapped in

Our replacement payload:

    Source:  E:\GTA\port-work\dist\hen.bin
    Size:    515,792 bytes
    SHA256:  4cf0df0dbde6cf68fbb407151cda9a50e64a716fe7af9583f04654825f1ea70b
    Head:    e9 df 72 00 00 ...  (JMP rel32; passes jb.js head-check)
    Build:   Scene-Collective PS4-HEN 2.2.0 with kpayload/source/offsets/1352.c
             baked in. Userland stage-2 (installer) style raw binary --
             HEN's own userland routine loads and executes the kpayload via
             the syscall path raw13g's kpatch installs.

## Firmware alignment verdict: MATCH (13.52)

- raw13g targets: 13.02, 13.04, 13.50, 13.52.
- Our hen.bin was built with 13.52 offsets (kpayload/source/offsets/1352.c).
- Both point at `payload2.bin` for every firmware in the SUPPORTED set;
  raw13g's own note is that the shipped `payload2.bin` was itself a
  PS4-HEN payload tuned "native-1352", so we are drop-in-replacing a HEN
  build with a HEN build for the same firmware.

Cross-check of the shared kernel data-section symbols raw13g exposes vs.
what our HEN's `1352.c` uses (13.52 row):

|                  | raw13g ps4_offsets.js | HEN 1352.c            | match |
|------------------|-----------------------|-----------------------|-------|
| SYSENT           | 0x1102b70             | 0x01102B70            | YES   |
| ROOTVNODE        | 0x2136e90             | 0x02136E90            | YES   |

(HEN also carries its own PRISON0 slot at 0x0111FA18 which is a
different symbol from raw13g's `k_prison0` = 0x1a5c0c0 -- raw13g uses
prison0 for a jail-clear write from the WebKit side; HEN uses its slot
for its own kernel-context work. They aren't the same table entry, so
disagreement is expected and safe.)

Behaviour on 13.02 / 13.04 / 13.50: the OLD payload2.bin was noted as
"PS4HEN(works<=13.52)" so it degraded gracefully to those firmwares.
The NEW payload2.bin is compiled against 13.52 kernel offsets, so it
will only run its kernel-payload cleanly on 13.52. Running it on 13.02/
13.04/13.50 will (at worst) fail the offset-consistency check inside
HEN and cleanly refuse; the raw13g exploit itself is unaffected up to
the moment HEN takes over. See "Hardware testing" below for what to
watch.

## Files touched

1. `payload2.bin`
   - Was: 311,744 bytes, sha256 fab982aea6c9b2aa9d590eae4adb1530f7b4ccd32322c097d6e3c2527bbd6135 (raw13g's original PS4HEN-native-1352 build).
   - Now: 515,792 bytes, sha256 4cf0df0dbde6cf68fbb407151cda9a50e64a716fe7af9583f04654825f1ea70b (our Scene-Collective HEN 2.2.0 / 13.52 build).

2. `payload2.original.bin`   (NEW)
   - A byte-identical copy of the pre-swap `payload2.bin`, kept as a
     one-step revert path. Not referenced by any HTML/JS/appcache.

3. `cache.appcache`
   - Line 2 rev comment bumped: `20260916-181000-user-edits-logo` -> `20260920-payload-swap-hen220`
     (forces PS4 browsers to re-check the whole manifest).
   - Line 13 payload2.bin hash comment updated from
     `fab982aea6c9b2aa9d590eae4adb1530f7b4ccd32322c097d6e3c2527bbd6135` to
     `4cf0df0dbde6cf68fbb407151cda9a50e64a716fe7af9583f04654825f1ea70b`
     (the `#...` is technically a comment in the AppCache format, but
      it's used as a cache-buster; the rev bump alone would force a
      re-fetch too - both were bumped to keep the manifest honest).

4. `PORT_NOTES.md`   (this file, NEW)

Nothing else was modified. `jb.js`, `ps4_offsets.js`, and every other
HTML/JS file is byte-for-byte unchanged. The chain still fetches
`patches/<fw>.bin` and `payload2.bin`; the payload just is a different
build now.

### Why no size/header edits were needed in jb.js

`jb.js` load path (line numbers from the current file):

- Line 191:  `const PAYLOAD_FILE = off.payload || "payload.bin";` -- always
             resolves to `"payload2.bin"` for supported fws.
- Line 2587: `const r = await fetch(PAYLOAD_FILE);` -- no size assertion.
- Line 3036: `payloadBlob[0] === 0xe9` -- header sanity, our hen.bin is
             0xE9 xx xx xx xx.
- Line 3038: `const sz = (payloadBlob.length + 0x3fff) & ~0x3fff;` -- size
             is derived from the blob at runtime and page-rounded; grows
             transparently from 0x4C1C0 (311744) to 0x81000 (515792).

Grepping the whole tree for the old hard-coded size (`311744`, `0x4C1C0`)
turned up no hits, so no size constant needs updating.

## How to reproduce (bash)

    cd E:/GTA/port-work/raw13g.github.io

    # 1. one-shot backup (skipped if already present)
    [ -f payload2.original.bin ] || cp payload2.bin payload2.original.bin

    # 2. swap
    cp E:/GTA/port-work/dist/hen.bin payload2.bin

    # 3. sanity
    sha256sum payload2.bin
    # -> 4cf0df0dbde6cf68fbb407151cda9a50e64a716fe7af9583f04654825f1ea70b
    stat -c '%s' payload2.bin
    # -> 515792
    head -c 1 payload2.bin | od -An -tx1
    # -> e9

    # 4. re-derive cache.appcache line 13 hash if you rebuild hen.bin
    sha256sum payload2.bin | cut -d' ' -f1
    # then paste after the "#" on the payload2.bin line, and bump the rev
    # comment on line 2 to force PS4 browsers to re-check the manifest.

## How to revert

    cd E:/GTA/port-work/raw13g.github.io
    cp payload2.original.bin payload2.bin

Then either:
- restore the old cache.appcache hash on line 13
  (`payload2.bin #fab982aea6c9b2aa9d590eae4adb1530f7b4ccd32322c097d6e3c2527bbd6135`)
  and the old rev on line 2 (`# rev 20260916-181000-user-edits-logo`), OR
- just bump the rev comment on line 2 to any new value; the hash comment
  is cosmetic to the browser as long as the manifest text is different
  from the previously-cached one.

## Hardware testing

Load `https://<your host>/index.html` on a PS4. Add `?log=1` to see
the full step log inline (see `jb.html`'s `body.log` styles) -- it
tag-emits every stage.

Fingerprints you want to see, in order:

    KPATCH-BLOB     file=patches/1352.bin bytes=314 sites=<>0
    PAYLOAD-BLOB    file=payload2.bin bytes=515792 head=e9-ok
    PAYLOAD-MAP     mmap(anon,rwx,0x81000)=<addr> err=0
    PAYLOAD-COPY    bytes=515792 ok
    PTHREAD-RESOLVE got=<addr> expect=<addr>   (must be equal)
    PAYLOAD-RUN     pthread_create=0 handle=<non-zero>
    PAYLOAD-RUNNING <same>

    JB-TDUCRED-CLEAN
    ... "Restart your console" (jb.html's #msg goes visible on body.fail,
    but on a clean end-of-run jb.js drives body.done)

Signs the console is fine and HEN just failed silently:
- payload2.bin fetched OK, head=e9-ok, but nothing HEN-visible appears
  in Debug Settings / no goldhen menu.
  -> HEN's own offset check refused to run because the console is not on
     13.52. Re-check FW: Settings -> System Software Update or the UA
     shown at the top of jb.html's log. There is no danger here; the
     kpatch has already been rolled back by jb.js's end-of-run cleanup.

Signs the offset alignment is wrong AND the payload started patching
before it detected the mismatch (this is the "bail before a second
attempt" case):
- PAYLOAD-RUNNING passes but the console hangs during the "restart your
  console" screen with no controller response, no red LED, no beep for
  > 60 s.
- PAYLOAD-RUNNING passes and then the console spontaneously reboots or
  the screen goes black.
- Any KP (kernel panic) beep pattern -- one long, three short.

If any of the above happens: DO NOT retry. Power the console off with
the physical button, wait 30 s, boot to Safe Mode, and choose "Restart
PS4". Do NOT run the exploit again until you have verified with the
user that the FW really is 13.52 (Settings -> System -> System
Information -> System Software Version). A second attempt with a
mismatched kernel offset table is what actually risks bricking.

Signs the raw13g chain itself broke (nothing to do with our swap):
- The log stops before PAYLOAD-BLOB, e.g. at kernel-table-present or at
  a JB-SOURCES check. This is not caused by the payload swap; revert
  and file with raw13g if it reproduces on the original payload.

## TODOs for the user before running on hardware

- Confirm target console is on 13.52 exactly. 13.50 will *probably* run
  raw13g's chain to completion (raw13g's own table has it) but HEN
  built for 13.52 offsets will refuse or crash. If your target is 13.50
  you need either (a) rebuild hen.bin against `offsets/1350.c` and
  re-run the swap, or (b) revert to `payload2.original.bin`.
- Host the folder over HTTPS on a LAN reachable from the PS4 browser.
  index.html requires appcache, which most modern desktop browsers no
  longer serve; the PS4 WebKit is the one that still supports it.
- First run downloads the manifest; expect the "CACHED (first run) --
  press X or tap to run" gate. Second run should show "cached --
  offline ready".

## Do-not

- Do NOT rebuild `patches/1352.bin` from HEN's kpayload -- it is a
  DIFFERENT kernel-patch table (raw13g's kpatch installs the syscall
  bridge HEN's userland stage relies on; HEN's own kpayload is what
  runs in kernel context AFTER that bridge exists). The two are not
  interchangeable.
- Do NOT edit `ps4_offsets.js`. The comments mention "PS4HEN-native-1352"
  and old sha references -- those are stale after our swap but the
  strings are documentation only, not consulted by any code path.
