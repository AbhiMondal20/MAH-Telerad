<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RadReport — Radiology Document Editor</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
/* ===== CSS VARIABLES ===== */
:root {
  --bg-base: #0a0e17;
  --bg-surface: #111827;
  --bg-card: #1a2235;
  --bg-hover: #1e2d45;
  --bg-active: #243352;
  --border: #2a3a55;
  --border-light: #354a6a;
  --text-primary: #e2eaf7;
  --text-secondary: #7f9ab8;
  --text-muted: #4a6080;
  --accent: #3b82f6;
  --accent-glow: rgba(59,130,246,0.25);
  --accent-green: #10b981;
  --accent-amber: #f59e0b;
  --accent-red: #ef4444;
  --accent-purple: #8b5cf6;
  --page-bg: #f8f9fc;
  --page-text: #1e293b;
  --ribbon-h: 96px;
  --status-h: 28px;
  --sidebar-w: 240px;
  --right-sidebar-w: 200px;
  --font-ui: 'DM Sans', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;
  --font-display: 'Playfair Display', serif;
  --transition: 150ms cubic-bezier(0.4,0,0.2,1);
}
[data-theme="light"] {
  --bg-base: #e8edf5;
  --bg-surface: #f1f5fb;
  --bg-card: #ffffff;
  --bg-hover: #e2e8f4;
  --bg-active: #d5dff0;
  --border: #c5d0e4;
  --border-light: #b5c3db;
  --text-primary: #1a2540;
  --text-secondary: #4a6080;
  --text-muted: #8fa3be;
  --accent-glow: rgba(59,130,246,0.15);
}

/* ===== RESET & BASE ===== */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { 
  font-family: var(--font-ui); background: var(--bg-base); color: var(--text-primary); 
  height: 100vh; overflow: hidden; display: flex; flex-direction: column; font-size: 13px;
  scroll-behavior: smooth;
}

/* ===== SCROLL IMPROVEMENTS & SMOOTH SCROLLING ===== */
*::-webkit-scrollbar { width: 6px; height: 6px; }
*::-webkit-scrollbar-track { background: transparent; }
*::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

/* ===== TOP BRAND BAR ===== */
#brand-bar {
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; padding: 8px 12px; gap: 8px; flex-shrink: 0; z-index: 100; flex-wrap: wrap;
}
.brand-logo { font-family: var(--font-display); font-size: 16px; color: var(--accent); letter-spacing: 0.02em; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.brand-logo svg { width: 18px; height: 18px; }
#brand-bar .doc-title { flex: 1; text-align: center; color: var(--text-secondary); font-size: 12px; font-weight: 500; letter-spacing: 0.04em; min-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#brand-bar .menu-group { display: flex; gap: 2px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
#brand-bar .menu-group::-webkit-scrollbar { display: none; }
.menu-btn { background: none; border: none; color: var(--text-secondary); font-family: var(--font-ui); font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; cursor: pointer; transition: background var(--transition), color var(--transition); white-space: nowrap; }
.menu-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
.brand-actions { display: flex; gap: 4px; align-items: center; }
.icon-btn { background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center; transition: background var(--transition), color var(--transition); }
.icon-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
.icon-btn svg { width: 15px; height: 15px; }

/* ===== MOBILE SPECIFIC MENU BUTTON ===== */
.mobile-menu-btn { display: none; }
.mobile-sidebar-header { display: none; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--bg-surface); }

/* ===== RIBBON ===== */
#ribbon { background: var(--bg-surface); border-bottom: 1px solid var(--border); flex-shrink: 0; z-index: 90; }
#ribbon-tabs { display: flex; padding: 0 8px; border-bottom: 1px solid var(--border); gap: 2px; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
#ribbon-tabs::-webkit-scrollbar { display: none; }
.ribbon-tab { background: none; border: none; color: var(--text-muted); font-family: var(--font-ui); font-size: 11px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; padding: 6px 12px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color var(--transition), border-color var(--transition); white-space: nowrap; }
.ribbon-tab:hover { color: var(--text-primary); }
.ribbon-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
#ribbon-content { padding: 4px 8px; min-height: 56px; overflow-x: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
#ribbon-content::-webkit-scrollbar { height: 4px; }
.ribbon-panel { display: none; flex-wrap: nowrap; gap: 2px; align-items: center; width: max-content; }
.ribbon-panel.active { display: flex; }
.ribbon-group { display: flex; align-items: center; gap: 1px; padding: 0 6px; border-right: 1px solid var(--border); }
.ribbon-group:last-child { border-right: none; }
.r-btn { background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px 7px; border-radius: 4px; font-family: var(--font-ui); font-size: 12px; display: flex; align-items: center; justify-content: center; gap: 4px; transition: background var(--transition), color var(--transition); white-space: nowrap; min-width: 28px; height: 28px; position: relative; }
.r-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
.r-btn.active { background: var(--bg-active); color: var(--accent); }
.r-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.r-btn.danger { color: var(--accent-red); }
.r-btn.success { color: var(--accent-green); }
.r-sep { width: 1px; height: 20px; background: var(--border); margin: 0 3px; }
.r-select { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary); font-family: var(--font-ui); font-size: 12px; padding: 4px 6px; border-radius: 4px; cursor: pointer; height: 28px; outline: none; transition: border-color var(--transition); }
.r-select:hover, .r-select:focus { border-color: var(--accent); }
.r-color-btn { width: 28px; height: 28px; border: 1px solid var(--border); border-radius: 4px; cursor: pointer; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.r-color-btn input[type=color] { position: absolute; width: 200%; height: 200%; top: -50%; left: -50%; cursor: pointer; border: none; outline: none; opacity: 0; }
.r-color-preview { width: 14px; height: 4px; border-radius: 2px; position: absolute; bottom: 4px; }

/* ===== MAIN LAYOUT ===== */
#main-layout { display: flex; flex: 1; overflow: hidden; position: relative; width: 100vw; max-width: 100%; }

/* ===== LEFT SIDEBAR ===== */
#left-sidebar { width: var(--sidebar-w); background: var(--bg-surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0; transition: transform var(--transition); }

/* MOBILE TAB FIX */
.sidebar-tabs { 
    display: flex; border-bottom: 1px solid var(--border); 
    overflow-x: auto; white-space: nowrap; scrollbar-width: none; 
    -webkit-overflow-scrolling: touch; scroll-behavior: smooth;
    position: sticky; top: 0; background: var(--bg-surface); z-index: 10;
}
.sidebar-tabs::-webkit-scrollbar { display: none; }
.sidebar-tab { 
    flex: 0 0 auto; background: none; border: none; color: var(--text-muted); font-family: var(--font-ui); font-size: 10px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; padding: 10px 12px; cursor: pointer; border-bottom: 2px solid transparent; transition: color var(--transition), border-color var(--transition); 
}
.sidebar-tab:hover { color: var(--text-primary); }
.sidebar-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

.sidebar-panel { display: none; flex: 1; overflow-y: auto; padding: 8px; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
.sidebar-panel.active { display: block; }
.sidebar-section-title { font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); padding: 8px 4px 4px; display: flex; justify-content: space-between; align-items: center; }
.template-item, .snippet-item { background: var(--bg-card); border: 1px solid var(--border); border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; cursor: pointer; transition: border-color var(--transition), background var(--transition); display: flex; flex-direction: column; position: relative; }
.template-item:hover, .snippet-item:hover { border-color: var(--accent); background: var(--bg-hover); }
.template-item-title { font-size: 11px; font-weight: 600; color: var(--text-primary); }
.template-item-sub { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
.macro-chip { display: inline-flex; align-items: center; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; padding: 3px 8px; font-family: var(--font-mono); font-size: 10px; color: var(--accent); cursor: pointer; margin: 2px; transition: background var(--transition), border-color var(--transition); }
.macro-chip:hover { background: var(--accent); color: white; border-color: var(--accent); }

.sidebar-search { width: 100%; background: var(--bg-base); border: 1px solid var(--border); color: var(--text-primary); padding: 6px 10px; border-radius: 4px; font-size: 11px; margin-bottom: 8px; outline: none; }
.sidebar-search:focus { border-color: var(--accent); }
.action-icon { opacity: 0; position: absolute; right: 8px; top: 8px; display: flex; gap: 4px; }
.template-item:hover .action-icon, .snippet-item:hover .action-icon { opacity: 1; }

/* ===== EDITOR AREA ===== */
#editor-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: var(--bg-base); max-width: 100vw; }
#mode-bar { display: flex; align-items: center; padding: 4px 12px; background: var(--bg-surface); border-bottom: 1px solid var(--border); gap: 4px; flex-shrink: 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
#mode-bar::-webkit-scrollbar { display: none; }
.mode-btn { background: none; border: 1px solid var(--border); color: var(--text-muted); font-family: var(--font-ui); font-size: 11px; font-weight: 600; letter-spacing: 0.04em; padding: 3px 10px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: all var(--transition); white-space: nowrap; }
.mode-btn:hover { color: var(--text-primary); border-color: var(--border-light); }
.mode-btn.active { background: var(--bg-active); border-color: var(--accent); color: var(--accent); }
.mode-btn svg { width: 12px; height: 12px; }
#mode-bar .spacer { flex: 1; }
.zoom-control { display: flex; align-items: center; gap: 4px; color: var(--text-muted); font-size: 11px; }
.zoom-control button { background: none; border: 1px solid var(--border); color: var(--text-secondary); width: 20px; height: 20px; border-radius: 3px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.zoom-control button:hover { background: var(--bg-hover); }
#zoom-display { min-width: 36px; text-align: center; font-family: var(--font-mono); font-size: 11px; }

/* ===== EDITOR SCROLL CONTAINER ===== */
#editor-scroll { 
    flex: 1; overflow-y: auto; overflow-x: hidden; background: var(--bg-base); 
    padding: 24px 16px; display: flex; justify-content: center; 
    -webkit-overflow-scrolling: touch; scroll-behavior: smooth;
}

/* ===== TRUE MULTI-PAGE FIXES ===== */
#document-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 24px;
    transform-origin: top center;
    transition: transform var(--transition);
}

.page-wrapper {
  width: 794px; 
  height: 1123px; /* STRICT A4 HEIGHT */
  background: var(--page-bg); 
  color: var(--page-text); 
  box-shadow: 0 4px 40px rgba(0,0,0,0.5); 
  border-radius: 2px; 
  padding: 72px 80px; 
  position: relative; 
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
}

.page-content {
  font-family: 'Georgia', serif; 
  font-size: 13px; 
  line-height: 1.7; 
  color: var(--page-text); 
  outline: none; 
  word-wrap: break-word;
  flex: 1;
  overflow: hidden; /* Prevent visual overflow, triggers pagination */
}

/* Page Footer styling */
.page-footer {
    position: absolute;
    bottom: 30px;
    left: 0;
    width: 100%;
    text-align: center;
    font-size: 11px;
    color: var(--text-muted);
    font-family: var(--font-ui);
    user-select: none;
}

/* Page Watermark */
.page-watermark { position: absolute; top: 8px; right: 12px; font-size: 9px; letter-spacing: 0.08em; text-transform: uppercase; color: #c5cce0; font-family: var(--font-ui); user-select: none; }

/* Internal Formatting */
.page-content h1 { font-size: 22px; font-weight: 700; margin: 16px 0 8px; }
.page-content h2 { font-size: 18px; font-weight: 700; margin: 14px 0 6px; }
.page-content h3 { font-size: 15px; font-weight: 700; margin: 12px 0 5px; }
.page-content p { margin: 0 0 6px; }
.page-content ul, .page-content ol { padding-left: 24px; margin: 6px 0; }
.page-content li { margin: 2px 0; }
.page-content blockquote { border-left: 3px solid var(--accent); padding: 8px 16px; margin: 12px 0; background: rgba(59,130,246,0.05); font-style: italic; }
.page-content table { border-collapse: collapse; width: 100%; margin: 12px 0; }
.page-content table td, .page-content table th { border: 1px solid #c5cce0; padding: 6px 10px; font-size: 12px; }
.page-content table th { background: #e8edf5; font-weight: 600; }
.page-content pre { background: #1e2535; color: #c8d3f0; padding: 12px 16px; border-radius: 6px; font-family: var(--font-mono); font-size: 11px; margin: 10px 0; overflow-x: auto; }
.page-content a { color: #2563eb; }
.page-content hr { border: none; border-top: 1px solid #c5cce0; margin: 16px 0; }

/* Manual Page Break Visual marker */
.manual-page-break {
    border-top: 2px dashed var(--accent);
    margin: 20px 0;
    position: relative;
}
.manual-page-break::after {
    content: "PAGE BREAK"; position: absolute; top: -8px; left: 50%; transform: translateX(-50%);
    background: var(--page-bg); padding: 0 10px; font-size: 10px; color: var(--accent); font-family: var(--font-mono);
}

/* ===== SOURCE & SPLIT ===== */
#source-editor { display: none; flex: 1; overflow: hidden; flex-direction: column; }
#source-editor.active { display: flex; }
#source-textarea { flex: 1; background: #0d1117; color: #c9d1d9; font-family: var(--font-mono); font-size: 12px; border: none; outline: none; padding: 16px; resize: none; line-height: 1.6; tab-size: 2; -webkit-overflow-scrolling: touch; }
#split-view { display: none; flex: 1; overflow: hidden; }
#split-view.active { display: flex; }
#split-left { flex: 1; overflow: hidden; display: flex; flex-direction: column; border-right: 2px solid var(--border); }
#split-right { flex: 1; overflow-y: auto; padding: 16px; background: var(--bg-base); -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
#split-preview { font-family: 'Georgia', serif; font-size: 12px; line-height: 1.6; color: var(--text-primary); max-width: 600px; word-wrap: break-word; }
#split-left #source-textarea { height: 100%; }
.split-label { background: var(--bg-surface); border-bottom: 1px solid var(--border); padding: 4px 12px; font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); font-weight: 600; flex-shrink: 0; }

/* ===== RIGHT SIDEBAR ===== */
#right-sidebar { width: var(--right-sidebar-w); background: var(--bg-surface); border-left: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0; }
.right-sidebar-section { border-bottom: 1px solid var(--border); padding: 10px 12px; }
.right-sidebar-section-title { font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
.stat-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.stat-label { font-size: 11px; color: var(--text-secondary); }
.stat-value { font-size: 11px; font-weight: 600; color: var(--text-primary); font-family: var(--font-mono); }
.outline-item { padding: 4px 8px; font-size: 11px; color: var(--text-secondary); cursor: pointer; border-radius: 3px; transition: background var(--transition), color var(--transition); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.outline-item:hover { background: var(--bg-hover); color: var(--text-primary); }
.outline-item.h1 { font-weight: 600; color: var(--text-primary); }
.outline-item.h2 { padding-left: 16px; }
.outline-item.h3 { padding-left: 24px; }
#outline-list { max-height: 200px; overflow-y: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
.save-status { display: flex; align-items: center; gap: 5px; font-size: 11px; }
.save-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent-green); }
.save-dot.unsaved { background: var(--accent-amber); }
.save-dot.saving { background: var(--accent-amber); animation: pulse 1s infinite; }

/* ===== STATUS BAR ===== */
#status-bar { height: var(--status-h); background: var(--accent); display: flex; align-items: center; padding: 0 12px; gap: 16px; flex-shrink: 0; overflow-x: auto; }
#status-bar::-webkit-scrollbar { display: none; }
.status-item { font-size: 11px; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 4px; font-family: var(--font-mono); white-space: nowrap; }
.status-item svg { width: 11px; height: 11px; opacity: 0.7; }
#status-bar .spacer { flex: 1; }
#cursor-pos { font-family: var(--font-mono); }

/* Custom Page Navigator in Status Bar */
.page-nav-btn { background: rgba(255,255,255,0.1); border: none; color: white; border-radius: 3px; cursor: pointer; padding: 2px 6px; transition: background var(--transition); }
.page-nav-btn:hover { background: rgba(255,255,255,0.2); }

/* ===== FIND/REPLACE PANEL ===== */
#find-panel { display: none; position: absolute; top: 0; right: 16px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 0 0 8px 8px; padding: 10px 12px; z-index: 200; min-width: 280px; box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
#find-panel.open { display: block; }
.find-row { display: flex; gap: 6px; margin-bottom: 6px; align-items: center; }
.find-input { flex: 1; background: var(--bg-surface); border: 1px solid var(--border); color: var(--text-primary); font-family: var(--font-mono); font-size: 12px; padding: 4px 8px; border-radius: 4px; outline: none; }
.find-input:focus { border-color: var(--accent); }
.find-btn { background: var(--bg-surface); border: 1px solid var(--border); color: var(--text-secondary); font-family: var(--font-ui); font-size: 11px; padding: 4px 8px; border-radius: 4px; cursor: pointer; white-space: nowrap; }
.find-btn:hover { border-color: var(--accent); color: var(--accent); }
.find-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px; padding: 0 4px; }

/* ===== VOICE INDICATOR ===== */
#voice-indicator { display: none; position: fixed; bottom: 40px; right: 20px; background: var(--accent-red); color: white; padding: 8px 14px; border-radius: 24px; font-size: 12px; font-weight: 600; align-items: center; gap: 8px; box-shadow: 0 4px 20px rgba(239,68,68,0.4); z-index: 500; animation: voicePulse 1.5s infinite; }
#voice-indicator.listening { display: flex; }
.voice-dot { width: 8px; height: 8px; background: white; border-radius: 50%; animation: voiceDot 0.8s infinite; }

/* ===== SWEETALERT SMALLER SIZING CUSTOMIZATION ===== */
.swal2-popup { width: 340px !important; padding: 16px !important; border-radius: 8px !important; background: var(--bg-card) !important; color: var(--text-primary) !important; font-family: var(--font-ui) !important; box-sizing: border-box !important; }
.swal2-title { font-size: 15px !important; margin-bottom: 10px !important; color: var(--text-primary) !important; }
.swal2-html-container { font-size: 12px !important; margin: 0 !important; }
.swal2-input, .swal2-select { height: 34px !important; padding: 0 10px !important; font-size: 13px !important; margin: 8px auto !important; background: var(--bg-surface) !important; color: var(--text-primary) !important; border-color: var(--border) !important; box-sizing: border-box !important; }
.swal2-textarea { height: 80px !important; padding: 10px !important; font-size: 13px !important; margin: 8px auto !important; background: var(--bg-surface) !important; color: var(--text-primary) !important; border-color: var(--border) !important; box-sizing: border-box !important; }
.swal2-actions { margin-top: 16px !important; }
.swal2-styled { padding: 6px 18px !important; font-size: 12px !important; margin: 0 4px !important; }

/* ===== ALL DEVICE RESPONSIVENESS ===== */
@media (max-width: 1024px) {
    #right-sidebar { width: 180px; }
    #left-sidebar { width: 220px; }
}
@media (max-width: 768px) {
    .mobile-menu-btn { display: flex; }
    
    /* Left sidebar overlay for mobile */
    #left-sidebar { 
        position: fixed; top: 0; left: 0; height: 100vh; width: 280px; 
        z-index: 99999; background: var(--bg-surface); 
        transform: translateX(-100%); transition: transform 0.3s ease; 
        box-shadow: none;
    }
    #left-sidebar.mobile-open { 
        transform: translateX(0); 
        box-shadow: 0 0 0 100vw rgba(0,0,0,0.6);
    }
    .mobile-sidebar-header { display: flex; }
    
    #right-sidebar { display: none !important; }
    #split-view { flex-direction: column; }
    #split-left { border-right: none; border-bottom: 2px solid var(--border); }
    #editor-scroll { padding: 10px !important; }
    
    /* Responsive Scale for Multi-Page Container */
    #document-container {
        /* Mobile scale down, keep aspect ratio */
        transform: scale(0.9);
        transform-origin: top center;
    }
}
@media (max-width: 500px) {
    #document-container { transform: scale(0.5); margin-top: -20px; }
    #editor-scroll { padding: 0 !important; }
}

/* ===== PRINT STYLES ===== */
@media print {
    @page { margin: 0; size: A4 portrait; }
    body { background: white; margin: 0; padding: 0; }
    #brand-bar, #ribbon, #left-sidebar, #right-sidebar, #status-bar, #mode-bar { display: none !important; }
    #editor-area { background: white; overflow: visible; display: block; }
    #editor-scroll { background: white; overflow: visible; padding: 0; display: block; }
    #document-container { transform: none !important; gap: 0; display: block; }
    .page-wrapper { 
        box-shadow: none; margin: 0; border-radius: 0; 
        page-break-after: always; /* Ensure printer breaks on this div */
        height: 1123px; width: 794px;
        position: relative;
    }
    .manual-page-break { display: none; }
}
</style>
</head>
<body>

<div id="brand-bar">
  <button class="icon-btn mobile-menu-btn" onclick="toggleMobileSidebar()" title="Menu">
    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
  <div class="brand-logo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/><path d="M8 12h4"/></svg>
    RadReport
  </div>
  <div class="menu-group">
    <button class="menu-btn" onclick="menuFile()">File</button>
    <button class="menu-btn" onclick="menuEdit()">Edit</button>
    <button class="menu-btn" onclick="menuView()">View</button>
    <button class="menu-btn" onclick="exportPDF()">Export</button>
    <button class="menu-btn" onclick="printDoc()">Print</button>
  </div>
  <div class="doc-title" id="doc-title-display"></div>
  <div class="brand-actions">
    <button class="icon-btn" onclick="toggleTheme()" title="Toggle theme">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
    </button>
    <button class="icon-btn" title="Autosave on">
      <svg id="save-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/></svg>
    </button>
  </div>
</div>

<div id="ribbon">
  <div id="ribbon-tabs">
    <button class="ribbon-tab active" data-tab="home">Home</button>
    <button class="ribbon-tab" data-tab="insert">Insert</button>
    <button class="ribbon-tab" data-tab="format">Format</button>
    <button class="ribbon-tab" data-tab="medical">Medical</button>
    <button class="ribbon-tab" data-tab="review">Review</button>
  </div>
  <div id="ribbon-content">

    <div class="ribbon-panel active" id="tab-home">
      <div class="ribbon-group">
        <button class="r-btn" onclick="execCmd('undo')" title="Undo (Ctrl+Z)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 2.83-6.36L3 9.5"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('redo')" title="Redo (Ctrl+Y)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 7v6h-6"/><path d="M21 13a9 9 0 1 1-2.83-6.36L21 9.5"/></svg>
        </button>
      </div>
      <div class="ribbon-group">
        <select class="r-select" id="font-family" onchange="execCmd('fontName', this.value)" style="width:110px">
          <option value="Georgia">Georgia</option>
          <option value="Times New Roman">Times New Roman</option>
          <option value="Arial">Arial</option>
          <option value="Calibri">Calibri</option>
          <option value="Courier New">Courier New</option>
          <option value="Verdana">Verdana</option>
        </select>
        <select class="r-select" id="font-size" onchange="execCmd('fontSize', this.value)" style="width:54px">
          <option value="1">8pt</option>
          <option value="2">10pt</option>
          <option value="3" selected>12pt</option>
          <option value="4">14pt</option>
          <option value="5">18pt</option>
          <option value="6">24pt</option>
          <option value="7">36pt</option>
        </select>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="execCmd('bold')" id="btn-bold" title="Bold"><b>B</b></button>
        <button class="r-btn" onclick="execCmd('italic')" id="btn-italic" title="Italic"><i>I</i></button>
        <button class="r-btn" onclick="execCmd('underline')" id="btn-underline" title="Underline" style="text-decoration:underline">U</button>
        <button class="r-btn" onclick="execCmd('strikeThrough')" id="btn-strikeThrough" title="Strikethrough" style="text-decoration:line-through">S</button>
        <button class="r-btn" onclick="execCmd('superscript')" title="Superscript">X<sup>2</sup></button>
        <button class="r-btn" onclick="execCmd('subscript')" title="Subscript">X<sub>2</sub></button>
        <div class="r-sep"></div>
        <div class="r-color-btn" title="Text color">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;color:#888;z-index:1"><text x="2" y="18" font-size="18" font-weight="bold">A</text></svg>
          <div class="r-color-preview" id="fg-preview" style="background:#000"></div>
          <input type="color" id="text-color" value="#000000" oninput="execCmd('foreColor', this.value); document.getElementById('fg-preview').style.background=this.value">
        </div>
        <div class="r-color-btn" title="Highlight color">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;z-index:1"><path d="M12 19l7-7-3-3-7 7v3h3z"/></svg>
          <div class="r-color-preview" id="bg-preview" style="background:#ffff00"></div>
          <input type="color" id="bg-color" value="#ffff00" oninput="execCmd('hiliteColor', this.value); document.getElementById('bg-preview').style.background=this.value">
        </div>
        <button class="r-btn danger" onclick="execCmd('removeFormat')" title="Clear formatting">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 3H5M9 3v4M15 3v4M9 7l1 10M15 7l-1 10M4 7h16"/></svg>
        </button>
      </div>
      <div class="ribbon-group">
        <select class="r-select" id="format-block" onchange="execCmd('formatBlock', this.value); this.value=''" style="width:90px">
          <option value="">Style…</option>
          <option value="p">Paragraph</option>
          <option value="h1">Heading 1</option>
          <option value="h2">Heading 2</option>
          <option value="h3">Heading 3</option>
          <option value="h4">Heading 4</option>
          <option value="blockquote">Blockquote</option>
          <option value="pre">Code Block</option>
        </select>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="execCmd('justifyLeft')" id="btn-justifyLeft" title="Align left">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('justifyCenter')" id="btn-justifyCenter" title="Center">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('justifyRight')" id="btn-justifyRight" title="Align right">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('justifyFull')" id="btn-justifyFull" title="Justify">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="execCmd('insertUnorderedList')" id="btn-insertUnorderedList" title="Bullet list">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="4" cy="18" r="1.5" fill="currentColor"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('insertOrderedList')" id="btn-insertOrderedList" title="Numbered list">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4H4M3 10h2M4 14v1l-1.5 1.5H5M3 18h2"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('indent')" title="Increase indent">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="7" y1="12" x2="21" y2="12"/><line x1="7" y1="18" x2="21" y2="18"/><polyline points="3 12 5 14 3 16"/></svg>
        </button>
        <button class="r-btn" onclick="execCmd('outdent')" title="Decrease indent">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="7" y1="12" x2="21" y2="12"/><line x1="7" y1="18" x2="21" y2="18"/><polyline points="7 12 5 10 7 8"/></svg>
        </button>
      </div>
    </div>

    <div class="ribbon-panel" id="tab-insert">
      <div class="ribbon-group">
        <button class="r-btn" onclick="insertNewPageBtn()" title="New Page">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg> New Page
        </button>
        <button class="r-btn" onclick="insertManualPageBreak()" title="Page Break (Ctrl+Enter)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12" stroke-dasharray="4 4"/><path d="M10 8l-4 4 4 4M14 8l4 4-4 4"/></svg> Page Break
        </button>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="insertTable()" title="Insert Table">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="12" y1="3" x2="12" y2="21"/></svg> Table
        </button>
        <button class="r-btn" onclick="document.getElementById('img-upload').click()" title="Insert Image">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg> Image
        </button>
        <input type="file" id="img-upload" accept="image/*" style="display:none" onchange="insertImageFile(this)">
        <button class="r-btn" onclick="showLinkModal()" title="Hyperlink">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> Link
        </button>
        <button class="r-btn" onclick="execCmd('insertHorizontalRule')" title="Horizontal Rule">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/></svg> HR
        </button>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="insertSpecialChars()" title="Special Characters">Ω Chars</button>
        <button class="r-btn" onclick="insertChecklistItem()" title="Checklist Item">☑ Check</button>
        <button class="r-btn" onclick="execCmd('insertHTML','<blockquote></blockquote>')" title="Blockquote">❝ Quote</button>
      </div>
    </div>

    <div class="ribbon-panel" id="tab-format">
      <div class="ribbon-group">
        <button class="r-btn" onclick="toggleFindPanel()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Find & Replace
        </button>
      </div>
    </div>

    <div class="ribbon-panel" id="tab-medical">
      <div class="ribbon-group">
        <button class="r-btn success" onclick="insertSection('CLINICAL INDICATION')">INDICATION</button>
        <button class="r-btn success" onclick="insertSection('TECHNIQUE')">TECHNIQUE</button>
        <button class="r-btn success" onclick="insertSection('FINDINGS')">FINDINGS</button>
        <button class="r-btn success" onclick="insertSection('IMPRESSION')">IMPRESSION</button>
        <button class="r-btn success" onclick="insertSection('RECOMMENDATION')">RECOMMENDATION</button>
      </div>
      <div class="ribbon-group" id="quick-templates-bar">
        </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="showMacroModal()">{{Macros}}</button>
        <button class="r-btn" id="voice-btn" onclick="toggleVoice()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v4M8 23h8"/></svg> Dictate
        </button>
      </div>
    </div>

    <div class="ribbon-panel" id="tab-review">
      <div class="ribbon-group">
        <button class="r-btn" onclick="execCmd('copy')">Copy</button>
        <button class="r-btn" onclick="execCmd('cut')">Cut</button>
        <button class="r-btn" onclick="execCmd('paste')">Paste</button>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="toggleFindPanel()">Find</button>
        <button class="r-btn" onclick="toggleFindPanel()">Replace</button>
      </div>
      <div class="ribbon-group">
        <button class="r-btn" onclick="exportHTML()">Export HTML</button>
        <button class="r-btn" onclick="exportDOCX()">Export DOCX</button>
        <button class="r-btn" onclick="exportPDF()">Export PDF</button>
      </div>
    </div>

  </div>
</div>

<div id="main-layout">

  <div id="left-sidebar">
    <div class="mobile-sidebar-header">
      <span style="font-weight:bold; font-size:14px; color:var(--text-primary)">Menu</span>
      <button class="icon-btn" onclick="toggleMobileSidebar()">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="sidebar-tabs">
      <button class="sidebar-tab active" data-side="templates">Tmpl</button>
      <button class="sidebar-tab" data-side="snippets">Snip</button>
      <button class="sidebar-tab" data-side="macros">Macro</button>
      <button class="sidebar-tab" data-side="dictate">Dictate</button>
    </div>
    
    <div class="sidebar-panel active" id="side-templates">
      <input type="text" class="sidebar-search" placeholder="Search Templates..." onkeyup="filterSidebar(this, 'templates-container')">
      <div style="display:flex; justify-content:flex-end; margin-bottom: 5px;">
          <button class="r-btn success" style="height:20px; font-size:10px; padding:2px 6px;" onclick="addManagerItem('template')">+ Add</button>
      </div>
      <div id="templates-container"></div>
    </div>

    <div class="sidebar-panel" id="side-snippets">
      <input type="text" class="sidebar-search" placeholder="Search Snippets..." onkeyup="filterSidebar(this, 'snippets-container')">
      <div style="display:flex; justify-content:flex-end; margin-bottom: 5px;">
          <button class="r-btn success" style="height:20px; font-size:10px; padding:2px 6px;" onclick="addManagerItem('snippet')">+ Add</button>
      </div>
      <div id="snippets-container"></div>
    </div>

    <div class="sidebar-panel" id="side-macros">
      <input type="text" class="sidebar-search" placeholder="Search Macros..." onkeyup="filterSidebar(this, 'macros-container')">
      <div id="macros-container"></div>
      <div style="margin-top:10px">
        <button class="r-btn success" style="width:100%;justify-content:center" onclick="showMacroModal()">Fill Macros…</button>
      </div>
    </div>

    <div class="sidebar-panel" id="side-dictate">
        <div class="sidebar-section-title">Voice Dictation</div>
        <select class="r-select" id="dictate-lang" style="width:100%; margin-bottom:10px;">
            <option value="en-US">English (US)</option>
            <option value="hi-IN">Hindi (India)</option>
            <option value="bn-IN">Bengali (India)</option>
        </select>
        <button class="btn-primary" id="btn-dictate-toggle" style="width:100%; margin-bottom:10px;" onclick="toggleVoice()">Start Dictation</button>
        <div class="sidebar-section-title" style="margin-top:10px">Voice Commands</div>
        <div class="snippet-item" style="cursor:default">
            <span style="font-size:10px; color:var(--text-secondary)">"New Paragraph"</span><br>
            <span style="font-size:10px; color:var(--text-secondary)">"Full Stop" / "Comma"</span><br>
            <span style="font-size:10px; color:var(--text-secondary)">"Findings" / "Impression"</span>
        </div>
    </div>
  </div>

  <div id="editor-area">
    <div id="mode-bar">
      <button class="mode-btn active" id="mode-rich" onclick="switchMode('rich')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 8h10M7 12h7M7 16h5"/></svg> Rich Text
      </button>
      <button class="mode-btn" id="mode-html" onclick="switchMode('html')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg> HTML
      </button>
      <button class="mode-btn" id="mode-split" onclick="switchMode('split')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="3" x2="12" y2="21"/></svg> Split
      </button>
      <button class="mode-btn" onclick="toggleFullScreen()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg> Full
      </button>
      <div class="spacer"></div>
      <div class="zoom-control">
        <button onclick="changeZoom(-10)">−</button>
        <span id="zoom-display">100%</span>
        <button onclick="changeZoom(10)">+</button>
        <button onclick="changeZoom(0, 100)" style="width:auto; padding:0 4px;" title="Reset">Reset</button>
      </div>
    </div>

    <div id="rich-view" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
      <div id="editor-scroll">
        <div id="document-container">
          <div class="page-wrapper">
            <div class="page-watermark">RADREPORT</div>
            <div class="page-content" contenteditable="true" spellcheck="true"></div>
            <div class="page-footer">Page 1</div>
          </div>
        </div>
      </div>
    </div>

    <div id="source-editor">
      <textarea id="source-textarea" spellcheck="false" oninput="syncFromSource()"></textarea>
    </div>

    <div id="split-view">
      <div id="split-left">
        <div class="split-label">HTML Source</div>
        <textarea id="split-textarea" style="flex:1;background:#0d1117;color:#c9d1d9;font-family:var(--font-mono);font-size:11px;border:none;outline:none;padding:12px;resize:none;line-height:1.6;tab-size:2" spellcheck="false" oninput="syncSplitPreview()"></textarea>
      </div>
      <div id="split-right">
        <div class="split-label">Preview</div>
        <div id="split-preview"></div>
      </div>
    </div>

  </div><div id="right-sidebar">
    <div class="right-sidebar-section">
      <div class="right-sidebar-section-title">Save Status</div>
      <div class="save-status">
        <div class="save-dot" id="save-dot"></div>
        <span id="save-text" style="font-size:11px;color:var(--text-secondary)">Saved</span>
      </div>
    </div>
    <div class="right-sidebar-section">
      <div class="right-sidebar-section-title">Statistics</div>
      <div class="stat-row"><span class="stat-label">Words</span><span class="stat-value" id="stat-words">0</span></div>
      <div class="stat-row"><span class="stat-label">Characters</span><span class="stat-value" id="stat-chars">0</span></div>
      <div class="stat-row"><span class="stat-label">Paragraphs</span><span class="stat-value" id="stat-paras">0</span></div>
      <div class="stat-row"><span class="stat-label">Read time</span><span class="stat-value" id="stat-read">0 min</span></div>
    </div>
    <div class="right-sidebar-section" style="flex:1;overflow:hidden;display:flex;flex-direction:column">
      <div class="right-sidebar-section-title">Document Outline</div>
      <div id="outline-list"></div>
    </div>
    <div class="right-sidebar-section">
      <div class="right-sidebar-section-title">Quick Actions</div>
      <button class="r-btn" style="width:100%;justify-content:center;margin-bottom:4px" onclick="exportPDF()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Export PDF
      </button>
      <button class="r-btn" style="width:100%;justify-content:center" onclick="printDoc()">Print</button>
    </div>
  </div>

</div><div id="status-bar">
  <div class="status-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
    <span id="sb-words">0 words</span>
  </div>
  <div class="status-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
    <span id="sb-chars">0 chars</span>
  </div>
  <div class="status-item spacer"></div>
  <div class="status-item" style="display:flex; gap:6px; align-items:center;">
      <button class="page-nav-btn" onclick="navPage(-1)" title="Previous Page">◀</button>
      <span id="sb-page-count">Page 1 of 1</span>
      <button class="page-nav-btn" onclick="navPage(1)" title="Next Page">▶</button>
  </div>
  <div class="status-item" id="cursor-pos">Ln 1, Col 1</div>
  <div class="status-item" id="sb-mode">Rich Text</div>
</div>

<div id="find-panel">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
    <span style="font-size:12px;font-weight:600;color:var(--text-primary)">Find & Replace</span>
    <button class="find-close" onclick="toggleFindPanel()">×</button>
  </div>
  <div class="find-row">
    <input class="find-input" type="text" id="find-input" placeholder="Find…" oninput="highlightFind()">
    <button class="find-btn" onclick="findNext()">↓</button>
    <button class="find-btn" onclick="findPrev()">↑</button>
  </div>
  <div class="find-row">
    <input class="find-input" type="text" id="replace-input" placeholder="Replace with…">
    <button class="find-btn" onclick="replaceCurrent()">Replace</button>
    <button class="find-btn" onclick="replaceAll()">All</button>
  </div>
  <div style="font-size:10px;color:var(--text-muted);margin-top:4px" id="find-count"></div>
</div>

<div id="voice-indicator">
  <div class="voice-dot"></div> Listening…
  <button onclick="toggleVoice()" style="background:none;border:none;color:white;cursor:pointer;font-size:14px">×</button>
</div>

<script>
// ===== SWEETALERT & TOAST SYSTEM =====
const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
    timerProgressBar: true, didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
function showNotif(msg, icon = 'success') { Toast.fire({ icon: icon, title: msg }); }

// ===== STATE & DATA MANAGERS =====
let currentMode = 'rich'; let zoomLevel = 100; let isListening = false; let recognition = null; let autoSaveTimer = null; let isDirty = false;

let macroValues = JSON.parse(localStorage.getItem('radreport_macros')) || {
  patient_name: 'John Doe', patient_age: '45', patient_gender: 'Male', patient_id: 'PT-2024-001',
  exam_date: new Date().toLocaleDateString(), exam_type: 'CT', referring_doctor: 'Dr. Smith',
  doctor_name: 'Dr. Radiologist', hospital_name: 'General Hospital', hospital_address: '123 Medical Drive'
};

const defaultTemplates = {
  'ct': { title: 'CT Abdomen & Pelvis', sub: 'IV contrast, standard', content: `<h2 style="font-weight:bold;font-size:14px;text-transform:uppercase;margin-bottom:16px">CT ABDOMEN AND PELVIS WITH IV CONTRAST</h2><p><strong>CLINICAL INDICATION:</strong><br>{{clinical_indication}}</p><p style="margin-top:12px"><strong>TECHNIQUE:</strong><br>Axial scans were acquired through the abdomen and pelvis following the administration of intravenous contrast material.</p><p style="margin-top:12px"><strong>FINDINGS:</strong></p><p><strong>Liver:</strong> Normal in size and attenuation. No focal hepatic lesion.</p><p><strong>Gallbladder:</strong> Unremarkable.</p><p><strong>Pancreas:</strong> Unremarkable.</p><p><strong>Spleen:</strong> Normal in size and attenuation.</p><p><strong>Kidneys:</strong> Normal in size and position. No hydronephrosis.</p><p><strong>Appendix:</strong> Normal caliber, no periappendiceal stranding.</p><p><strong>Bowel:</strong> No obstruction.</p><p style="margin-top:12px"><strong>IMPRESSION:</strong></p><ol style="padding-left:20px"><li>No acute intraabdominal pathology.</li></ol>` }
};
let savedTemplates = JSON.parse(localStorage.getItem('radreport_templates')) || defaultTemplates;

const defaultSnippets = [
  { label: 'Normal liver', text: 'Liver is normal in size, shape and echotexture. No focal hepatic lesion identified.' },
  { label: 'Hepatomegaly', text: 'Hepatomegaly noted. No focal lesion identified. Portal vein is patent.' },
  { label: 'Normal appendix', text: 'Appendix is normal in caliber without periappendiceal inflammatory changes. No appendicolith seen.' },
  { label: 'Clear chest', text: 'Lungs are clear bilaterally. No pneumothorax or pleural effusion. Cardiomediastinal silhouette is normal.' }
];
let savedSnippets = JSON.parse(localStorage.getItem('radreport_snippets')) || defaultSnippets;

// Generate Data UI
function renderSidebars() {
    const tContainer = document.getElementById('templates-container'); const quickBar = document.getElementById('quick-templates-bar');
    tContainer.innerHTML = ''; quickBar.innerHTML = '';
    Object.entries(savedTemplates).forEach(([k, v]) => {
        tContainer.innerHTML += `<div class="template-item search-target" onclick="applyTemplate('${k}')"><div class="template-item-title">${v.title}</div><div class="template-item-sub">${v.sub}</div><div class="action-icon" onclick="event.stopPropagation(); deleteManagerItem('template', '${k}')"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="var(--accent-red)" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></div></div>`;
        quickBar.innerHTML += `<button class="r-btn" onclick="applyTemplate('${k}')">${v.title}</button>`;
    });

    const sContainer = document.getElementById('snippets-container'); sContainer.innerHTML = '';
    savedSnippets.forEach((s, idx) => {
        sContainer.innerHTML += `<div class="snippet-item search-target" onclick="insertSnippet('${s.text.replace(/'/g, "\\'")}')"><div class="template-item-title">${s.label}</div><div class="action-icon" onclick="event.stopPropagation(); deleteManagerItem('snippet', ${idx})"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="var(--accent-red)" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></div></div>`;
    });

    const mContainer = document.getElementById('macros-container'); mContainer.innerHTML = '';
    Object.keys(macroValues).forEach(k => {
        mContainer.innerHTML += `<span class="macro-chip search-target" onclick="insertMacro('${k}')">{{${k}}}</span>`;
    });
}
renderSidebars();

function filterSidebar(input, containerId) {
    const term = input.value.toLowerCase();
    document.getElementById(containerId).querySelectorAll('.search-target').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
}

function addManagerItem(type) {
    if(type === 'template') {
        Swal.fire({ title: 'Add Template', html: '<input id="swal-t-id" class="swal2-input" placeholder="ID (e.g. ct-head)"><input id="swal-t-title" class="swal2-input" placeholder="Title"><input id="swal-t-sub" class="swal2-input" placeholder="Subtitle">', focusConfirm: false, showCancelButton: true, preConfirm: () => { return { id: document.getElementById('swal-t-id').value, title: document.getElementById('swal-t-title').value, sub: document.getElementById('swal-t-sub').value, content: getFullContent() } }
        }).then(res => { if(res.isConfirmed && res.value.id) { savedTemplates[res.value.id] = { title: res.value.title, sub: res.value.sub, content: res.value.content || '<p>New</p>' }; localStorage.setItem('radreport_templates', JSON.stringify(savedTemplates)); renderSidebars(); showNotif('Template Added'); } });
    } else if (type === 'snippet') {
        Swal.fire({ title: 'Add Snippet', html: '<input id="swal-s-label" class="swal2-input" placeholder="Label"><textarea id="swal-s-text" class="swal2-textarea" placeholder="Snippet Text"></textarea>', focusConfirm: false, showCancelButton: true, preConfirm: () => { return { label: document.getElementById('swal-s-label').value, text: document.getElementById('swal-s-text').value } }
        }).then(res => { if(res.isConfirmed && res.value.label) { savedSnippets.push({ label: res.value.label, text: res.value.text }); localStorage.setItem('radreport_snippets', JSON.stringify(savedSnippets)); renderSidebars(); showNotif('Snippet Added'); } });
    }
}
function deleteManagerItem(type, key) {
    Swal.fire({ title: 'Delete ' + type + '?', text: "You can't revert this!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            if(type === 'template') { delete savedTemplates[key]; localStorage.setItem('radreport_templates', JSON.stringify(savedTemplates)); }
            if(type === 'snippet') { savedSnippets.splice(key, 1); localStorage.setItem('radreport_snippets', JSON.stringify(savedSnippets)); }
            renderSidebars(); showNotif('Deleted');
        }
    });
}

function menuFile() { Swal.fire({ title: 'File Menu', showCancelButton: true, showConfirmButton: false, html: `<div style="display:flex;flex-direction:column;gap:10px;"><button class="btn-primary" onclick="Swal.close(); confirmNewReport()">New Report</button><button class="btn-primary" onclick="Swal.close(); doAutoSave(); showNotif('Saved')">Save Draft</button><button class="btn-secondary" onclick="Swal.close(); exportHTML()">Export HTML</button><button class="btn-secondary" onclick="Swal.close(); exportDOCX()">Export DOCX</button><button class="btn-secondary" onclick="Swal.close(); exportPDF()">Export PDF</button><button class="btn-secondary" onclick="Swal.close(); printDoc()">Print</button></div>` }); }
function confirmNewReport() { Swal.fire({ title: 'Create new report?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes' }).then((result) => { if (result.isConfirmed) { setFullContent(''); updateStats(); showNotif('New report created'); }}); }
function menuEdit() { Swal.fire({ title: 'Edit Menu', showCancelButton: true, showConfirmButton: false, html: `<div style="display:flex;flex-direction:column;gap:10px;"><button class="btn-secondary" onclick="Swal.close(); execCmd('undo')">Undo</button><button class="btn-secondary" onclick="Swal.close(); execCmd('redo')">Redo</button><button class="btn-secondary" onclick="Swal.close(); execCmd('cut')">Cut</button><button class="btn-secondary" onclick="Swal.close(); execCmd('copy')">Copy</button><button class="btn-secondary" onclick="Swal.close(); execCmd('selectAll')">Select All</button></div>` }); }
function menuView() { Swal.fire({ title: 'View Menu', showCancelButton: true, showConfirmButton: false, html: `<div style="display:flex;flex-direction:column;gap:10px;"><button class="btn-secondary" onclick="Swal.close(); changeZoom(10)">Zoom In</button><button class="btn-secondary" onclick="Swal.close(); changeZoom(-10)">Zoom Out</button><button class="btn-secondary" onclick="Swal.close(); changeZoom(0, 100)">Reset Zoom</button><button class="btn-secondary" onclick="Swal.close(); toggleFullScreen()">Full Screen</button></div>` }); }
function toggleFullScreen() { if (!document.fullscreenElement) { document.documentElement.requestFullscreen(); } else { if (document.exitFullscreen) { document.exitFullscreen(); } } }

function toggleMobileSidebar() { document.getElementById('left-sidebar').classList.toggle('mobile-open'); }
document.getElementById('left-sidebar').addEventListener('click', function(e) { if (e.offsetX > this.offsetWidth && window.innerWidth <= 768) { toggleMobileSidebar(); } });

// ===== TRUE MULTI-PAGE & PAGINATION ENGINE =====
let currentPageIndex = 0;

function createNewPageDOM() {
    const wrapper = document.createElement('div');
    wrapper.className = 'page-wrapper';
    wrapper.innerHTML = `
        <div class="page-watermark">RADREPORT</div>
        <div class="page-content" contenteditable="true" spellcheck="true"></div>
        <div class="page-footer">Page X</div>
    `;
    // Attach event listeners to new contenteditable
    const content = wrapper.querySelector('.page-content');
    content.addEventListener('input', () => { schedulePagination(); scheduleAutoSave(); updateStats(); });
    content.addEventListener('paste', handlePaste);
    content.addEventListener('focus', () => { 
        const pages = Array.from(document.querySelectorAll('.page-wrapper'));
        currentPageIndex = pages.indexOf(wrapper);
    });
    return wrapper;
}

function updatePageNumbers() {
    const pages = document.querySelectorAll('.page-wrapper');
    pages.forEach((p, idx) => {
        p.querySelector('.page-footer').textContent = `Page ${idx + 1}`;
    });
    const curr = currentPageIndex + 1;
    document.getElementById('sb-page-count').textContent = `Page ${curr > pages.length ? pages.length : curr} of ${pages.length}`;
}

function navPage(direction) {
    const pages = document.querySelectorAll('.page-wrapper');
    currentPageIndex += direction;
    if(currentPageIndex < 0) currentPageIndex = 0;
    if(currentPageIndex >= pages.length) currentPageIndex = pages.length - 1;
    pages[currentPageIndex].scrollIntoView({ behavior: 'smooth' });
    pages[currentPageIndex].querySelector('.page-content').focus();
    updatePageNumbers();
}

let isPaginating = false;
let paginatorTimer = null;
function schedulePagination() {
    clearTimeout(paginatorTimer);
    paginatorTimer = setTimeout(paginate, 100);
}

function paginate() {
    if(isPaginating) return;
    isPaginating = true;
    
    const container = document.getElementById('document-container');
    let pages = container.querySelectorAll('.page-content');
    let hasChanges = false;
    
    for(let i=0; i<pages.length; i++) {
        let page = pages[i];
        
        // Wrap loose text nodes in paragraphs to allow block shifting
        page.childNodes.forEach(node => {
            if (node.nodeType === 3 && node.textContent.trim().length > 0) {
                const p = document.createElement('p');
                node.parentNode.insertBefore(p, node);
                p.appendChild(node);
            }
        });

        // Forced Manual Page Break logic
        const manualBreak = page.querySelector('.manual-page-break');
        if (manualBreak) {
            let wrapper = page.closest('.page-wrapper');
            let nextWrapper = wrapper.nextElementSibling;
            if(!nextWrapper) {
                nextWrapper = createNewPageDOM();
                wrapper.after(nextWrapper);
                pages = container.querySelectorAll('.page-content');
            }
            let nextPage = nextWrapper.querySelector('.page-content');
            
            // Move everything AFTER the manual break to the next page
            let node = manualBreak.nextSibling;
            while(node) {
                let next = node.nextSibling;
                nextPage.appendChild(node);
                node = next;
            }
            manualBreak.remove();
            hasChanges = true;
        }

        // Overflow Handle
        while(page.scrollHeight > page.clientHeight && page.childNodes.length > 1) {
            let wrapper = page.closest('.page-wrapper');
            let nextWrapper = wrapper.nextElementSibling;
            if(!nextWrapper) {
                nextWrapper = createNewPageDOM();
                wrapper.after(nextWrapper);
                pages = container.querySelectorAll('.page-content'); 
            }
            let nextPage = nextWrapper.querySelector('.page-content');
            
            // Move last child to next page top
            nextPage.insertBefore(page.lastChild, nextPage.firstChild);
            hasChanges = true;
        }
        
        // Underflow Handle
        let wrapper = page.closest('.page-wrapper');
        let nextWrapper = wrapper.nextElementSibling;
        if(nextWrapper) {
            let nextPage = nextWrapper.querySelector('.page-content');
            // Try to pull content back up if there's room
            while(nextPage.firstChild) {
                page.appendChild(nextPage.firstChild);
                if(page.scrollHeight > page.clientHeight) {
                    // Oops, it overflowed again. Put it back.
                    nextPage.insertBefore(page.lastChild, nextPage.firstChild);
                    break;
                }
                hasChanges = true;
            }
            // Clean up empty trailing pages
            if(nextPage.childNodes.length === 0 || nextPage.innerHTML.trim() === '<br>' || nextPage.innerHTML.trim() === '') {
                nextWrapper.remove();
                pages = container.querySelectorAll('.page-content');
                hasChanges = true;
            }
        }
    }
    
    updatePageNumbers();
    isPaginating = false;
    if(hasChanges) updateStats();
}

function insertNewPageBtn() {
    execCmd('insertHTML', '<div class="manual-page-break" style="page-break-after:always;"></div><p><br></p>');
    schedulePagination();
    setTimeout(() => navPage(1), 150); // Scroll to new page
}

function insertManualPageBreak() {
    execCmd('insertHTML', '<div class="manual-page-break" style="page-break-after:always;"></div><p><br></p>');
    schedulePagination();
}

// Global Content Accessors
function getFullContent() {
    return Array.from(document.querySelectorAll('.page-content')).map(p => p.innerHTML).join('');
}
function getFullText() {
    return Array.from(document.querySelectorAll('.page-content')).map(p => p.innerText).join('\n');
}
function setFullContent(html) {
    const container = document.getElementById('document-container');
    container.innerHTML = '';
    const wrapper = createNewPageDOM();
    container.appendChild(wrapper);
    wrapper.querySelector('.page-content').innerHTML = html;
    paginate();
    updateStats();
}

// Ensure the first page has event listeners attached on load
document.querySelectorAll('.page-content').forEach(p => {
    p.addEventListener('input', () => { schedulePagination(); scheduleAutoSave(); updateStats(); });
    p.addEventListener('paste', handlePaste);
    p.addEventListener('focus', function() {
        const pages = Array.from(document.querySelectorAll('.page-wrapper'));
        currentPageIndex = pages.indexOf(this.closest('.page-wrapper'));
        updatePageNumbers();
    });
});

// ===== EXACT MS WORD PASTE WITH FONT/SIZE DETECTION =====
function updateToolbarState() {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;
    let node = selection.anchorNode;
    // Check if we are inside any page
    if (!node || (node.nodeType === 1 && !node.closest('.page-content')) && (node.nodeType === 3 && !node.parentNode.closest('.page-content'))) return;
    
    try {
        ['bold', 'italic', 'underline', 'strikeThrough', 'insertUnorderedList', 'insertOrderedList'].forEach(cmd => {
            const btn = document.getElementById('btn-' + cmd);
            if(btn) btn.classList.toggle('active', document.queryCommandState(cmd));
        });
        
        let el = node.nodeType === 3 ? node.parentNode : node;
        if(el && el.nodeType === 1) {
            const computed = window.getComputedStyle(el);
            
            let font = computed.fontFamily.split(',')[0].replace(/['"]/g, '');
            if (font) {
                const fontSelect = document.getElementById('font-family');
                let exists = Array.from(fontSelect.options).find(o => font.toLowerCase().includes(o.value.toLowerCase()));
                if (exists) fontSelect.value = exists.value;
            }
            
            let pxSize = parseFloat(computed.fontSize);
            let htmlSize = '3';
            if (pxSize <= 11) htmlSize = '1'; else if (pxSize <= 14) htmlSize = '2'; else if (pxSize <= 17) htmlSize = '3'; else if (pxSize <= 20) htmlSize = '4'; else if (pxSize <= 25) htmlSize = '5'; else if (pxSize <= 33) htmlSize = '6'; else htmlSize = '7';
            document.getElementById('font-size').value = htmlSize;

            const align = computed.textAlign;
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'].forEach(cmd => {
                const btn = document.getElementById('btn-' + cmd);
                if(btn) btn.classList.remove('active');
            });
            if (align === 'center') document.getElementById('btn-justifyCenter')?.classList.add('active');
            else if (align === 'right') document.getElementById('btn-justifyRight')?.classList.add('active');
            else if (align === 'justify') document.getElementById('btn-justifyFull')?.classList.add('active');
            else document.getElementById('btn-justifyLeft')?.classList.add('active');
        }

        const block = document.queryCommandValue('formatBlock');
        if(block) document.getElementById('format-block').value = block;
    } catch(e) {}
    updateCursorPos();
}

document.addEventListener('selectionchange', updateToolbarState);

function handlePaste(e) {
    e.preventDefault();
    let text = (e.originalEvent || e).clipboardData.getData('text/html');
    if (!text) {
        text = (e.originalEvent || e).clipboardData.getData('text/plain');
        document.execCommand('insertText', false, text);
        return;
    }
    // Deep MS Word cleanup while keeping crucial styles
    text = text.replace(/<(meta|link|script|style)[^>]*>[\s\S]*?<\/\1>/gi, '').replace(/<(meta|link)[^>]*>/gi, '');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = text;
    document.execCommand('insertHTML', false, tempDiv.innerHTML);
    showNotif('Word formatting preserved', 'info');
    schedulePagination();
    scheduleAutoSave();
    setTimeout(updateToolbarState, 50);
}

function execCmd(cmd, val = null) { 
    const sel = window.getSelection();
    if(sel.rangeCount) {
        const node = sel.anchorNode.nodeType === 3 ? sel.anchorNode.parentNode : sel.anchorNode;
        if(!node.closest('.page-content')){
             document.querySelector('.page-content').focus();
        }
    } else {
        document.querySelector('.page-content').focus();
    }
    document.execCommand(cmd, false, val); 
    schedulePagination();
    scheduleAutoSave(); 
    updateStats(); 
}

function switchMode(mode) {
  const richView = document.getElementById('rich-view'); const sourceEditor = document.getElementById('source-editor'); const splitView = document.getElementById('split-view'); const sourceTa = document.getElementById('source-textarea'); const splitTa = document.getElementById('split-textarea');
  
  if (currentMode === 'html') setFullContent(sourceTa.value); 
  else if (currentMode === 'split') setFullContent(splitTa.value);
  
  currentMode = mode; richView.style.display = 'none'; sourceEditor.classList.remove('active'); splitView.classList.remove('active');
  document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active')); document.getElementById('mode-' + mode).classList.add('active');
  
  if (mode === 'rich') { richView.style.display = 'flex'; document.getElementById('sb-mode').textContent = 'Rich Text'; } 
  else if (mode === 'html') { sourceEditor.classList.add('active'); sourceTa.value = getFullContent(); document.getElementById('sb-mode').textContent = 'HTML Source'; } 
  else { splitView.classList.add('active'); splitTa.value = getFullContent(); syncSplitPreview(); document.getElementById('sb-mode').textContent = 'Split View'; }
}

function syncFromSource() { setFullContent(document.getElementById('source-textarea').value); scheduleAutoSave(); }
function syncSplitPreview() { const val = document.getElementById('split-textarea').value; document.getElementById('split-preview').innerHTML = val; setFullContent(val); scheduleAutoSave(); }

function changeZoom(delta, absolute = null) { 
    if (absolute !== null) zoomLevel = absolute; 
    else zoomLevel = Math.max(50, Math.min(200, zoomLevel + delta)); 
    document.getElementById('document-container').style.transform = `scale(${zoomLevel/100})`; 
    document.getElementById('zoom-display').textContent = zoomLevel + '%'; 
    document.getElementById('sb-zoom').textContent = zoomLevel + '%'; 
}

function applyTemplate(key) {
  const tmpl = savedTemplates[key]; if (!tmpl) return;
  Swal.fire({ title: 'Apply template?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes' }).then((result) => {
      if (result.isConfirmed) { setFullContent(tmpl.content); showNotif('Template applied'); }
  });
  if(window.innerWidth <= 768) toggleMobileSidebar(); 
}

function setPageLayout(type) { 
    const pages = document.querySelectorAll('.page-wrapper'); 
    pages.forEach(page => {
        if (type === 'a4') { page.style.width = '794px'; page.style.height = '1123px'; } 
        else { page.style.width = '816px'; page.style.height = '1056px'; } 
    });
    paginate();
    showNotif(type.toUpperCase() + ' layout'); 
}

function toggleFindPanel() { document.getElementById('find-panel').classList.toggle('open'); if (document.getElementById('find-panel').classList.contains('open')) document.getElementById('find-input').focus(); }
function highlightFind() { const q = document.getElementById('find-input').value; document.getElementById('find-count').textContent = q ? `Searching…` : ''; if (!q) return; const text = getFullText(); const re = new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'), 'gi'); document.getElementById('find-count').textContent = [...text.matchAll(re)].length ? `${[...text.matchAll(re)].length} found` : 'Not found'; }
function findNext() { highlightFind(); } function findPrev() { highlightFind(); }
function replaceAll() { 
    const q = document.getElementById('find-input').value; const r = document.getElementById('replace-input').value; if (!q) return; 
    const re = new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'), 'gi'); 
    setFullContent(getFullContent().replace(re, r)); 
    showNotif('Replaced all'); 
}
function replaceCurrent() { replaceAll(); }

function updateStats() {
  const text = getFullText(); const words = text.trim() ? text.trim().split(/\s+/).length : 0; const chars = text.length; 
  const paras = Array.from(document.querySelectorAll('.page-content p')).length; const readTime = Math.max(1, Math.round(words / 200));
  document.getElementById('stat-words').textContent = words; document.getElementById('stat-chars').textContent = chars; document.getElementById('stat-paras').textContent = paras; document.getElementById('stat-read').textContent = readTime + ' min'; document.getElementById('sb-words').textContent = words + ' words'; document.getElementById('sb-chars').textContent = chars + ' chars';
  updateOutline();
}
function updateOutline() {
  const list = document.getElementById('outline-list'); list.innerHTML = ''; let count = 0;
  document.querySelectorAll('.page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content strong').forEach(h => {
    if (count > 12) return; const div = document.createElement('div'); div.className = `outline-item ${h.tagName.toLowerCase()}`; div.textContent = h.textContent.substring(0, 28); div.onclick = () => h.scrollIntoView({ behavior: 'smooth' }); list.appendChild(div); count++;
  });
}

function scheduleAutoSave() { isDirty = true; document.getElementById('save-dot').className = 'save-dot unsaved'; document.getElementById('save-text').textContent = 'Unsaved'; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(doAutoSave, 3000); }
setInterval(doAutoSave, 30000);
function doAutoSave() {
  if(!isDirty) return; try { localStorage.setItem('radreport_draft', getFullContent()); localStorage.setItem('radreport_saved', Date.now()); } catch(e) {}
  document.getElementById('save-dot').className = 'save-dot saving'; document.getElementById('save-text').textContent = 'Saving…'; setTimeout(() => { document.getElementById('save-dot').className = 'save-dot'; document.getElementById('save-text').textContent = 'Saved'; isDirty = false; }, 600);
}

window.addEventListener('load', () => {
  try { const saved = localStorage.getItem('radreport_draft'); if (saved) { Swal.fire({ title: 'Draft Found', text: "Restore unsaved report?", icon: 'question', showCancelButton: true, confirmButtonText: 'Restore' }).then((result) => { if (result.isConfirmed) { setFullContent(saved); updateStats(); } else { setFullContent(defaultTemplates['ct'].content); }}); } else { setFullContent(defaultTemplates['ct'].content); } } catch(e) {} updateStats();
});

function showMacroModal() {
  const fieldsHtml = Object.entries(macroValues).map(([k, v]) => `<div style="margin-bottom:8px;text-align:left;"><label style="font-size:11px;color:var(--text-secondary)">{{${k}}}</label><input class="swal2-input" id="macro-${k}" value="${v}"></div>`).join('');
  Swal.fire({ title: 'Macros', html: fieldsHtml, showCancelButton: true, confirmButtonText: 'Apply', preConfirm: () => { Object.keys(macroValues).forEach(k => { const el = document.getElementById(`macro-${k}`); if (el) macroValues[k] = el.value; }); localStorage.setItem('radreport_macros', JSON.stringify(macroValues)); renderSidebars(); applyMacros(); } });
}
function applyMacros() { let html = getFullContent(); Object.entries(macroValues).forEach(([k, v]) => { html = html.replace(new RegExp(`{{${k}}}`, 'gi'), v); }); setFullContent(html); showNotif('Macros applied'); }
function showLinkModal() { const sel = window.getSelection().toString(); Swal.fire({ title: 'Insert Link', html: `<input id="swal-l-url" class="swal2-input" placeholder="https://..."><input id="swal-l-txt" class="swal2-input" placeholder="Display Text" value="${sel}">`, preConfirm: () => { const url = document.getElementById('swal-l-url').value; const txt = document.getElementById('swal-l-txt').value || url; if (url) execCmd('insertHTML', `<a href="${url}" target="_blank">${txt}</a>`); } }); }

function toggleVoice() {
  if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) { Swal.fire('Error', 'Not supported.', 'error'); return; }
  if (isListening) { recognition && recognition.stop(); isListening = false; document.getElementById('voice-indicator').classList.remove('listening'); document.getElementById('btn-dictate-toggle').textContent = 'Start Dictation'; showNotif('Stopped', 'info'); return; }
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition; recognition = new SR(); recognition.continuous = true; recognition.interimResults = true; recognition.lang = document.getElementById('dictate-lang').value || 'en-US';
  recognition.onresult = e => { let interim = ''; for (let i = e.resultIndex; i < e.results.length; i++) { if (e.results[i].isFinal) { let text = e.results[i][0].transcript; text = text.replace(/\bnew paragraph\b/gi, '<br><br>').replace(/\bfull stop\b/gi, '.').replace(/\bcomma\b/gi, ',').replace(/\bfindings\b/gi, '\nFINDINGS:\n').replace(/\bimpression\b/gi, '\nIMPRESSION:\n'); execCmd('insertHTML', text + ' '); } } };
  recognition.onend = () => { if(isListening) { recognition.start(); return; } document.getElementById('voice-indicator').classList.remove('listening'); };
  recognition.start(); isListening = true; document.getElementById('voice-indicator').classList.add('listening'); document.getElementById('btn-dictate-toggle').textContent = 'Stop Dictation'; showNotif('Started', 'success');
}

function exportHTML() { const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Report</title></head><body>${getFullContent()}</body></html>`; download('report.html', html, 'text/html'); showNotif('Exported'); }
function exportDOCX() { const html = `<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Doc</title></head><body>${getFullContent()}</body></html>`; const blob = new Blob(['\ufeff', html], { type: 'application/msword' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'RadReport.doc'; document.body.appendChild(link); link.click(); document.body.removeChild(link); showNotif('Exported'); }

/* PDF Multiple Page Support Fixes */
function exportPDF() {
  const element = document.getElementById('document-container');
  html2pdf().set({
      margin: 0, // Margin is handled natively by our wrapper
      filename: 'RadReport.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'px', format: [794, 1123], orientation: 'portrait' }, // Match actual wrapper size exactly
      pagebreak: { mode: ['css', 'legacy'] }
  }).from(element).save().then(() => { showNotif('PDF Export Complete'); });
}

/* Native Print Multiple Page Support Fixes */
function printDoc() {
    const content = getFullContent();
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head><title>Radiology Report</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body{font-family:Georgia,serif;font-size:13px;line-height:1.7;color:#000;}
        h1,h2,h3,strong{font-weight:bold;}
        table{border-collapse:collapse;width:100%;page-break-inside:avoid;}
        td,th{border:1px solid #ccc;padding:6px 10px;}
        p, li, h1, h2, h3 { page-break-inside: avoid; }
        .manual-page-break { height: 0; border: none; margin: 0; background: transparent; page-break-after: always; }
        .manual-page-break::after { display: none; }
    </style></head><body>${content}</body></html>`);
    win.document.close(); win.focus();
    setTimeout(() => { win.print(); win.close(); }, 500);
}

function download(filename, content, type) { const a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([content], { type })); a.download = filename; a.click(); }
function toggleTheme() { const html = document.documentElement; html.setAttribute('data-theme', html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }

document.querySelectorAll('.ribbon-tab').forEach(btn => { btn.addEventListener('click', () => { document.querySelectorAll('.ribbon-tab, .ribbon-panel').forEach(b => b.classList.remove('active')); btn.classList.add('active'); document.getElementById('tab-' + btn.dataset.tab).classList.add('active'); }); });
document.querySelectorAll('.sidebar-tab').forEach(btn => { btn.addEventListener('click', () => { document.querySelectorAll('.sidebar-tab, .sidebar-panel').forEach(b => b.classList.remove('active')); btn.classList.add('active'); document.getElementById('side-' + btn.dataset.side).classList.add('active'); }); });

function updateCursorPos() { 
    try { 
        const sel = window.getSelection(); if (!sel.rangeCount) return; 
        const range = sel.getRangeAt(0); 
        const pageContent = range.startContainer.nodeType === 3 ? range.startContainer.parentNode.closest('.page-content') : range.startContainer.closest('.page-content');
        if(!pageContent) return;
        const pre = range.cloneRange(); pre.selectNodeContents(pageContent); pre.setEnd(range.startContainer, range.startOffset); 
        const lines = pre.toString().split('\n'); document.getElementById('cursor-pos').textContent = `Ln ${lines.length}, Col ${lines[lines.length-1].length+1}`; 
    } catch(e) {} 
}

document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey)) { switch(e.key.toLowerCase()) { 
      case 'z': e.preventDefault(); execCmd('undo'); break; 
      case 'y': e.preventDefault(); execCmd('redo'); break; 
      case 'f': e.preventDefault(); toggleFindPanel(); break; 
      case 's': e.preventDefault(); doAutoSave(); showNotif('Saved'); break; 
      case 'p': e.preventDefault(); printDoc(); break; 
      case 'enter': e.preventDefault(); insertManualPageBreak(); break; // Ctrl+Enter page break
  } }
  if (e.key === 'Escape' && document.getElementById('find-panel').classList.contains('open')) toggleFindPanel();
});
</script>
</body>
</html>