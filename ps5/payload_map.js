// @ts-check

const CUSTOM_ACTION_APPCACHE_REMOVE = "appcache-remove";

/**
 * @typedef {Object} PayloadInfo
 * @property {string} displayTitle
 * @property {string} description
 * @property {string} fileName - path relative to the payloads folder
 * @property {string} author
 * @property {string} projectSource
 * @property {string} binarySource - should be direct download link to the included version, so that you can verify the hashes
 * @property {string} version
 * @property {string[]?} [supportedFirmwares] - optional, these are interpreted as prefixes, so "" would match all, and "4." would match 4.xx, if not set, the payload is assumed to be compatible with all firmwares
 * @property {number?} [toPort] - optional, if the payload should be sent to "127.0.0.1:<port>" instead of loading directly, if specified it'll show up in webkit-only mode too
 * @property {string?} [customAction]
 */

/**
 * @type {PayloadInfo[]}
*/
const payload_map = [
    // { // auto-loaded
    //     displayTitle: "PS5 Payload ELF Loader",
    //     description: "Uses port 9021. Persistent network elf loader",
    //     fileName: "elfldr.elf",
    //     ////author: "john-tornblom",
    //     projectSource: "https://github.com/ps5-payload-dev/elfldr",
    //     binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
    //     ////version: "?",
    //     supportedFirmwares: ["1.", "2.", "3.", "4.", "5."]
    // },
    {
        displayTitle: "الغاء القفل عن العاب",
        description: "لازله القفل عن العاب وتنزيل بكج",
        fileName: "ps5-kstuff.bin",
        ////author: "sleirsgoevy",
        projectSource: "https://github.com/sleirsgoevy/ps4jb-payloads/tree/bd-jb/ps5-kstuff",
        binarySource: "https://github.com/sleirsgoevy/ps4jb2/blob/3e6053c3e4c691a9ccdc409172293a81de00ad7f/ps5-kstuff.bin",
        ////version: "3e6053c",
        supportedFirmwares: ["3.", "4."]
    },
    {
        displayTitle: "الغاء قفل العاب",
        description: "لازله القفل عن العاب وتنزيل بكجات",
        fileName: "byepervisor.elf",
        ////author: "SpecterDev, ChendoChap, flatz, fail0verflow, Znullptr, kiwidog, sleirsgoevy, EchoStretch, LightningMods, BestPig, zecoxao", 
        projectSource: "https://github.com/EchoStretch/Byepervisor",
        binarySource: "https://github.com/EchoStretch/Byepervisor/actions/runs/11946215784",
        ////version: "5a0d933",
        supportedFirmwares: ["1.00", "1.01", "1.02", "1.05", "1.12", "1.14", "2.00", "2.20", "2.25", "2.26", "2.30", "2.50", "2.70"],
        toPort: 9021
    },
	{
        displayTitle: "تفعيل ريموت بلاي",
        description: "تفعيل ريموت بلاي على أجهزة بلاي ستيشن 5 التي تعمل بنظام جيلبريك.",
        fileName: "rp-get-pin.elf",
        ////author: "idlesauce",
        projectSource: "https://github.com/idlesauce/ps5-remoteplay-get-pin",
        binarySource: "https://github.com/idlesauce/ps5-remoteplay-get-pin/releases/tag/v0.1",
        ////version: "0.1",
        toPort: 9021
    },
    {
        displayTitle: "libhijacker game-patch",
        description: "تصحيح الألعاب المدعومة لتعمل بمعدلات إطارات أعلى، وإضافة قوائم تصحيح الأخطاء إلى بعض الألعاب.",
        fileName: "libhijacker-game-patch.v1.160.elf",
        ////author: "illusion0001, astrelsky",
        projectSource: "https://github.com/illusion0001/libhijacker",
        binarySource: "https://github.com/illusion0001/libhijacker-game-patch/releases/tag/1.160-75ab26a3",
        ////version: "1.160",
        supportedFirmwares: ["3.", "4."]
    },
    {
        displayTitle: "websrv",
        description: "خادم لنقل الملفات بين اجهزه الحاسوب عبر الإنترنت واجهزه  كونسل عبر منفذ يعمل : 8080",
        fileName: "websrv.elf",
        ////author: "john-tornblom",
        projectSource: "https://github.com/ps5-payload-dev/websrv/releases",
        binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
        ////version: "0.14",
        toPort: 9021
    },
    {
        displayTitle: "ftpsrv",
        description: "خادم لنقل الملفات بين اجهزه الحاسوب عبر الإنترنت واجهزه  كونسل عبر منفذ يعمل : 2121",
        fileName: "ftpsrv.elf",
        ////author: "john-tornblom",
        projectSource: "https://github.com/ps5-payload-dev/ftpsrv",
        binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
        ////version: "0.11",
        toPort: 9021
    },
    {
        displayTitle: "klogsrv",
        description: "خادم لنقل الملفات بين اجهزه الحاسوب عبر الإنترنت واجهزه  كونسل عبر منفذ يعمل : 3232",
        fileName: "klogsrv.elf",
        ////author: "john-tornblom",
        projectSource: "https://github.com/ps5-payload-dev/klogsrv/releases",
        binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
        ////version: "0.5",
        toPort: 9021
    },
    {
        displayTitle: "shsrv",
        description: "خادم لنقل الملفات بين اجهزه الحاسوب عبر الإنترنت واجهزه  كونسل عبر منفذ يعمل : 2323",
        fileName: "shsrv.elf",
        ////author: "john-tornblom",
        projectSource: "https://github.com/ps5-payload-dev/shsrv/releases",
        binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
        ////version: "0.12",
        toPort: 9021
    },
    {
        displayTitle: "gdbsrv",
        description: "خادم لنقل الملفات بين اجهزه الحاسوب عبر الإنترنت واجهزه  كونسل عبر منفذ يعمل : 2159  ",
        fileName: "gdbsrv.elf",
        ////author: "john-tornblom",
        projectSource: "https://github.com/ps5-payload-dev/gdbsrv/releases",
        binarySource: "https://github.com/ps5-payload-dev/pacbrew-repo/actions/runs/11597570082",
        ////version: "0.4-1",
        toPort: 9021
    },
    {
        displayTitle: "ps5debug",
        description: "هذا مصحح أخطاء مخصص لاجهزه بلاي ستيشين 5.",
        fileName: "ps5debug.elf",
        ////author: "SiSTR0, ctn123",
        projectSource: "https://github.com/GoldHEN/ps5debug",
        binarySource: "https://github.com/GoldHEN/ps5debug/releases/download/1.0b1/ps5debug_v1.0b1.7z",
        ////version: "1.0b1",
        supportedFirmwares: ["3.", "4."]
    },
    {
        displayTitle: "ps5debug",
        description: "يعرض إصدار نواة النظام، وإصدار نظام التشغيل، وإصدار حزمة تطوير البرمجيات (SDK).",
        fileName: "ps5debug_dizz.elf",
        ////author: "Dizz, astrelsky, John Tornblom, SiSTR0, golden, idlesauce",
        projectSource: "https://github.com/idlesauce/ps5debug",
        binarySource: "https://github.com/idlesauce/ps5debug/releases/download/v0.0.1/ps5debug.elf",
        ////version: "0.0.1",
        toPort: 9021
    },
    {
        displayTitle: "ps5-versions",
        description: "(SDK) يعرض إصدار نواة النظام، وإصدار نظام التشغيل، وإصدار حزمة تطوير البرمجيات  .",
        fileName: "ps5-versions.elf",
        ////author: "SiSTRo",
        projectSource: "https://github.com/SiSTR0/ps5-versions",
        binarySource: "https://github.com/SiSTR0/ps5-versions/releases/download/v1.0/ps5-versions.elf",
        ////version: "1.0",
        supportedFirmwares: ["1.", "2.", "3.", "4."]
    },
    {
        // https://github.com/Storm21CH/PS5_Browser_appCache_remove
        displayTitle: "إزالة ذاكرة التخزين المؤقت المتصفح",
        description: "حذف ملف كاش خاص بصفحه جيلبريك",
        fileName: "",
        ////author: "Storm21CH, idlesauce",
        projectSource: "",
        binarySource: "",
        ////version: "1.0",
        customAction: CUSTOM_ACTION_APPCACHE_REMOVE
    }

];
