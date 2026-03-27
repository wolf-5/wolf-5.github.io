import React, { useState, useCallback, useRef } from 'react';
import { GoogleGenAI } from "@google/genai";
import { 
  Upload, 
  FileText, 
  Download, 
  Play, 
  CheckCircle2, 
  AlertCircle, 
  Loader2, 
  X,
  Languages,
  FileDown
} from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import JSZip from 'jszip';
import { cn } from '@/src/lib/utils';

// --- Constants & Prompt ---
const BASE_PROMPT = `Translate ONLY English game text lines into Arabic.

The input consists of numbered lines. You MUST strictly follow all rules below.

--------------------------------------------------
GENERAL RULE
--------------------------------------------------
• Translate ONLY the lines written in English.
• Any non-English line (Japanese, Chinese, etc.) must remain EXACTLY unchanged.

--------------------------------------------------
1. CORE TRANSLATION RULES & EXCEPTIONS
--------------------------------------------------
Your MAIN task is to translate the FULL English text of each line into Arabic.

The following elements must remain COMPLETELY UNCHANGED (do NOT translate or modify them):

• Placeholders / Variables:
  Any text inside [], {{ }}, < >, %% %% or starting with $
  Examples: [PlayerName], {{COUNT}}, <TARGET_INFO>, %%VALUE%%

• Formatting tags (the tags themselves):
  HTML-like tags such as <br>, <i>, <b>, <cf>
  Custom paired tags:
  [f][/f], <pl></pl>, {{n}}{{/n}}, {{g|ID}}{{/g}}

• Words containing underscores (_):
  Example: item_name, texture_file, face_skin

• Paragraph / color codes:
  Codes like §G, §R, §1 must remain unchanged.
  Text AFTER the code must be translated if it is normal text.
  Example:
  §GTranslate this → §Gترجم هذا
  BUT:
  §c[ITEM_NAME] → unchanged

• Special symbols and formatting characters:
  **, ***, ☢, « », ÷
  Escape sequences MUST remain EXACT:
  \\n  \\r  \\t
  (Keep quantity and position exactly)

--------------------------------------------------
2. TRANSLATION AROUND TAGS
--------------------------------------------------
• Text BEFORE and AFTER placeholders or tags MUST be translated.
• Tags do NOT interrupt translation.

Correct:
[Action] Translate this! → [Action] ترجم هذا!

• Text INSIDE paired tags MUST be translated:
<i>Translate this</i> → <i>ترجم هذا</i>

EXCEPTION:
If the content inside the tag is ONLY a placeholder, leave it unchanged:
<i>[VARIABLE]</i>

--------------------------------------------------
3. QUOTATION MARKS (")
--------------------------------------------------
• Text inside quotation marks MUST be translated.
• Quotation marks themselves must remain unchanged and in the same position.

Example:
"Translate this!" → "ترجم هذا!"

--------------------------------------------------
4. LINE COUNT & OUTPUT
--------------------------------------------------
• Return EXACTLY {line_count} numbered lines.
• Each input line must have ONE and ONLY ONE output line.
• Do NOT skip lines.
• Do NOT add explanations or extra text.

--------------------------------------------------
5. TONE
--------------------------------------------------
• NEVER use formal Arabic.
• ALWAYS use direct / informal Arabic.

--------------------------------------------------
6. CAPITALIZED / ENGLISH TERMS
--------------------------------------------------
• Capitalized or English terms MUST be translated into Arabic.
• Do NOT keep the English word in parentheses.
• Replace the English term entirely.

--------------------------------------------------
7. PUNCTUATION & FORMATTING
--------------------------------------------------
• Preserve original punctuation, symbols, spacing, indentation, and list markers.
• Line-ending punctuation must remain exactly the same.

--------------------------------------------------
8. SENTENCES WITH VARIABLES
--------------------------------------------------
• Rewrite translations to sound like complete, natural Arabic sentences.
• Variables must NOT break sentence flow.

--------------------------------------------------
9. DEFINITE ARTICLE USAGE
--------------------------------------------------
• Use correct Arabic definite articles (ال).
• Adjust sentence structure so grammar remains correct even with variables.

--------------------------------------------------
10. DO NOT TRANSLATE PIPE FUNCTIONS
--------------------------------------------------
• Do NOT translate any text containing:
  |plural(one=..., other=...)
  or anything matching:
  |*.*(*.*)

Leave such lines COMPLETELY unchanged.

--------------------------------------------------
11. FIXED REPLACEMENTS
--------------------------------------------------
• If the word "Credits" appears, replace it immediately with:
  تعريب محمد فضل

• Apply the following literal replacements (whole words only):

'الآلهة' → 'الزعماء'
'للإلهة' → 'للزعماء'
'آلهة' → 'زعماء'
'إله' → 'زعيم'
'اله' → 'زعيم'
'إلهة' → 'زعيم'
'إلهي' → 'زعيم'
'إلهية' → 'زعيم'
'الإلهي' → 'القيادي'
'الإلهية' → 'القيادية'
'بالإله' → 'بالزعيم'
'الله' → 'الحاكم'
'الإله' → 'الزعيم'

--------------------------------------------------
OUTPUT
--------------------------------------------------
• Output ONLY the Arabic translation with line numbers.
• No comments. No explanations. No extra text.
# =================================================`;

// --- Types ---
interface FileProgress {
  name: string;
  status: 'pending' | 'processing' | 'completed' | 'error';
  progress: number;
  content?: string;
  translatedContent?: string;
  error?: string;
}

const BATCH_SIZE = 50; // Number of lines per API call

export default function App() {
  const [files, setFiles] = useState<FileProgress[]>([]);
  const [isTranslating, setIsTranslating] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const fileList = e.target.files;
    if (fileList) {
      const selectedFiles = Array.from(fileList) as File[];
      const newFiles: FileProgress[] = selectedFiles.map(file => ({
        name: file.name,
        status: 'pending' as const,
        progress: 0,
        content: '',
      }));
      
      // Read file contents
      selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (event) => {
          const content = event.target?.result as string;
          setFiles(prev => {
            const updated = [...prev];
            const targetIndex = updated.findIndex(f => f.name === file.name);
            if (targetIndex !== -1) {
              updated[targetIndex].content = content;
            }
            return updated;
          });
        };
        reader.readAsText(file);
      });

      setFiles(prev => [...prev, ...newFiles]);
    }
  };

  const removeFile = (name: string) => {
    setFiles(prev => prev.filter(f => f.name !== name));
  };

  const translateBatch = async (ai: GoogleGenAI, lines: string[], startIdx: number) => {
    const numberedLines = lines.map((line, i) => `${startIdx + i + 1}. ${line}`).join('\n');
    const prompt = BASE_PROMPT.replace('{line_count}', lines.length.toString()) + '\n\n' + numberedLines;

    try {
      const response = await ai.models.generateContent({
        model: "gemini-3.1-pro-preview",
        contents: [{ role: 'user', parts: [{ text: prompt }] }],
      });

      const resultText = response.text || '';
      // Extract lines, removing the line numbers
      const translatedLines = resultText.split('\n')
        .map(line => line.replace(/^\d+\.\s*/, ''))
        .filter(line => line.trim() !== '');

      return translatedLines;
    } catch (error) {
      console.error("Translation error:", error);
      throw error;
    }
  };

  const startTranslation = async () => {
    if (files.length === 0) return;
    setIsTranslating(true);

    const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY! });

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (file.status === 'completed') continue;

      setFiles(prev => {
        const updated = [...prev];
        updated[i].status = 'processing';
        return updated;
      });

      try {
        const lines = file.content?.split(/\r?\n/) || [];
        const translatedLines: string[] = [];
        
        for (let j = 0; j < lines.length; j += BATCH_SIZE) {
          const batch = lines.slice(j, j + BATCH_SIZE);
          const translatedBatch = await translateBatch(ai, batch, j);
          translatedLines.push(...translatedBatch);
          
          const progress = Math.min(Math.round(((j + batch.length) / lines.length) * 100), 100);
          setFiles(prev => {
            const updated = [...prev];
            updated[i].progress = progress;
            return updated;
          });
        }

        setFiles(prev => {
          const updated = [...prev];
          updated[i].status = 'completed';
          updated[i].translatedContent = translatedLines.join('\n');
          return updated;
        });
      } catch (error: any) {
        setFiles(prev => {
          const updated = [...prev];
          updated[i].status = 'error';
          updated[i].error = error.message || 'Unknown error occurred';
          return updated;
        });
      }
    }

    setIsTranslating(false);
  };

  const downloadFile = (file: FileProgress) => {
    if (!file.translatedContent) return;
    const blob = new Blob([file.translatedContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `translated_${file.name}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  const downloadAll = async () => {
    const zip = new JSZip();
    files.forEach(file => {
      if (file.translatedContent) {
        zip.file(`translated_${file.name}`, file.translatedContent);
      }
    });
    const content = await zip.generateAsync({ type: 'blob' });
    const url = URL.createObjectURL(content);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'translated_files.zip';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  return (
    <div className="min-h-screen bg-[#FDFCFB] text-[#1A1A1A] font-sans selection:bg-[#FF6321]/20">
      {/* Header */}
      <header className="border-b border-[#1A1A1A]/10 px-6 py-8 md:px-12">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div>
            <div className="flex items-center gap-3 mb-4">
              <div className="bg-[#FF6321] p-2 rounded-lg">
                <Languages className="text-white w-6 h-6" />
              </div>
              <span className="text-[11px] uppercase tracking-[0.2em] font-bold text-[#FF6321]">
                AI Localization Tool
              </span>
            </div>
            <h1 className="text-5xl md:text-7xl font-light tracking-tight leading-[0.9]">
              Game Translator <span className="italic font-serif">Pro</span>
            </h1>
          </div>
          <p className="max-w-md text-[#1A1A1A]/60 text-sm leading-relaxed">
            Professional game text translation from English to Arabic. 
            Powered by Gemini 3.1 Pro with strict adherence to game localization standards.
          </p>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-6 py-12 md:px-12 grid grid-cols-1 lg:grid-cols-12 gap-12">
        {/* Upload Section */}
        <div className="lg:col-span-5 space-y-8">
          <div 
            className={cn(
              "group relative border-2 border-dashed border-[#1A1A1A]/20 rounded-3xl p-12 transition-all duration-300 hover:border-[#FF6321] hover:bg-[#FF6321]/5 cursor-pointer flex flex-col items-center justify-center text-center",
              isTranslating && "opacity-50 pointer-events-none"
            )}
            onClick={() => fileInputRef.current?.click()}
          >
            <input 
              type="file" 
              ref={fileInputRef} 
              onChange={handleFileChange} 
              multiple 
              className="hidden" 
              accept=".txt,.json,.csv,.strings"
            />
            <div className="w-16 h-16 bg-[#1A1A1A]/5 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <Upload className="w-8 h-8 text-[#1A1A1A]/40 group-hover:text-[#FF6321]" />
            </div>
            <h3 className="text-xl font-medium mb-2">Drop your files here</h3>
            <p className="text-[#1A1A1A]/40 text-sm">Supports .txt, .strings, .json, .csv</p>
          </div>

          <div className="bg-white border border-[#1A1A1A]/10 rounded-3xl p-8 space-y-6">
            <h4 className="text-[11px] uppercase tracking-[0.2em] font-bold text-[#1A1A1A]/40">
              Translation Rules
            </h4>
            <ul className="space-y-4 text-sm">
              {[
                "Preserve all placeholders like [PlayerName]",
                "Keep HTML tags and formatting intact",
                "Informal Arabic tone (Direct)",
                "Specific terminology replacements",
                "Credits → تعريب محمد فضل"
              ].map((rule, i) => (
                <li key={i} className="flex items-start gap-3">
                  <div className="w-1.5 h-1.5 rounded-full bg-[#FF6321] mt-1.5" />
                  <span className="text-[#1A1A1A]/80">{rule}</span>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Files List & Progress */}
        <div className="lg:col-span-7 space-y-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-2xl font-medium">
              Files <span className="text-[#1A1A1A]/40 ml-2">{files.length}</span>
            </h2>
            <div className="flex gap-3">
              {files.length > 0 && !isTranslating && (
                <button
                  onClick={startTranslation}
                  className="bg-[#1A1A1A] text-white px-6 py-3 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-[#FF6321] transition-colors duration-300"
                >
                  <Play className="w-4 h-4" />
                  Start Translation
                </button>
              )}
              {files.some(f => f.status === 'completed') && (
                <button
                  onClick={downloadAll}
                  className="border border-[#1A1A1A] text-[#1A1A1A] px-6 py-3 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-[#1A1A1A] hover:text-white transition-all duration-300"
                >
                  <FileDown className="w-4 h-4" />
                  Download All
                </button>
              )}
            </div>
          </div>

          <div className="space-y-4">
            <AnimatePresence mode="popLayout">
              {files.length === 0 ? (
                <motion.div 
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  className="h-64 flex flex-col items-center justify-center border border-[#1A1A1A]/5 rounded-3xl bg-[#1A1A1A]/[0.02] text-[#1A1A1A]/30 italic"
                >
                  No files uploaded yet
                </motion.div>
              ) : (
                files.map((file) => (
                  <motion.div
                    key={file.name}
                    layout
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    className="bg-white border border-[#1A1A1A]/10 rounded-2xl p-6 flex items-center gap-6"
                  >
                    <div className="w-12 h-12 bg-[#1A1A1A]/5 rounded-xl flex items-center justify-center shrink-0">
                      <FileText className="w-6 h-6 text-[#1A1A1A]/40" />
                    </div>
                    
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between mb-2">
                        <h4 className="font-medium truncate pr-4">{file.name}</h4>
                        <div className="flex items-center gap-3">
                          {file.status === 'completed' && (
                            <button 
                              onClick={() => downloadFile(file)}
                              className="text-[#FF6321] hover:underline text-sm font-medium flex items-center gap-1"
                            >
                              <Download className="w-4 h-4" />
                              Download
                            </button>
                          )}
                          {!isTranslating && (
                            <button 
                              onClick={() => removeFile(file.name)}
                              className="text-[#1A1A1A]/20 hover:text-red-500 transition-colors"
                            >
                              <X className="w-4 h-4" />
                            </button>
                          )}
                        </div>
                      </div>

                      {/* Progress Bar */}
                      <div className="relative h-1.5 w-full bg-[#1A1A1A]/5 rounded-full overflow-hidden">
                        <motion.div 
                          initial={{ width: 0 }}
                          animate={{ width: `${file.progress}%` }}
                          className={cn(
                            "absolute top-0 left-0 h-full transition-all duration-500",
                            file.status === 'error' ? "bg-red-500" : "bg-[#FF6321]"
                          )}
                        />
                      </div>

                      <div className="flex items-center justify-between mt-2">
                        <span className="text-[10px] uppercase tracking-wider font-bold text-[#1A1A1A]/40">
                          {file.status === 'processing' ? `Translating... ${file.progress}%` : file.status}
                        </span>
                        {file.status === 'completed' && <CheckCircle2 className="w-4 h-4 text-green-500" />}
                        {file.status === 'error' && (
                          <div className="flex items-center gap-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">
                            <AlertCircle className="w-3 h-3" />
                            Error
                          </div>
                        )}
                        {file.status === 'processing' && <Loader2 className="w-4 h-4 text-[#FF6321] animate-spin" />}
                      </div>
                    </div>
                  </motion.div>
                ))
              )}
            </AnimatePresence>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="max-w-7xl mx-auto px-6 py-12 md:px-12 border-t border-[#1A1A1A]/10 mt-12">
        <div className="flex flex-col md:flex-row justify-between items-center gap-6 text-[#1A1A1A]/40 text-xs tracking-widest uppercase font-bold">
          <span>Game Translator Pro v1.0</span>
          <span>Powered by Google Gemini 3.1 Pro</span>
          <span>© 2026 Localization Services</span>
        </div>
      </footer>
    </div>
  );
}
