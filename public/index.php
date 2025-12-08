<?php
// index.php - simple UI
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>StudyBuddy Smart Quiz Generator</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .wrapper {
      width: 100%;
      max-width: 700px;
    }

    .container {
      background: white;
      border-radius: 20px;
      padding: 50px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-icon {
      font-size: 48px;
      margin-bottom: 12px;
    }

    h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 36px;
      font-weight: 800;
      color: #2d3748;
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .subtitle {
      color: #718096;
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .note {
      color: #a0aec0;
      font-size: 14px;
      line-height: 1.6;
    }

    .form-group {
      margin-bottom: 28px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 10px;
      color: #2d3748;
      font-size: 15px;
    }

    .form-hint {
      font-size: 13px;
      color: #a0aec0;
      margin-top: 4px;
    }

    textarea {
      width: 100%;
      height: 160px;
      padding: 14px;
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      resize: vertical;
      transition: all 0.3s ease;
      color: #2d3748;
    }

    textarea:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: #f7fafc;
    }

    textarea::placeholder {
      color: #cbd5e0;
    }

    input[type="file"] {
      display: block;
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      border: 2px dashed #cbd5e0;
      background: #f7fafc;
      cursor: pointer;
      font-size: 14px;
      color: #4a5568;
      transition: all 0.3s ease;
    }

    input[type="file"]::file-selector-button {
      background: #667eea;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      margin-right: 12px;
      font-size: 14px;
      transition: 0.3s ease;
    }

    input[type="file"]::file-selector-button:hover {
      background: #5568d3;
    }

    input[type="file"]:hover {
      border-color: #667eea;
      background: #edf2f7;
    }

    .number-inputs {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    input[type="number"] {
      padding: 12px;
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      font-size: 14px;
      font-weight: 600;
      color: #2d3748;
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
    }

    input[type="number"]:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: #f7fafc;
    }

    .button-group {
      display: flex;
      gap: 12px;
      margin-top: 32px;
    }

    input[type="submit"],
    button {
      flex: 1;
      padding: 14px 24px;
      border-radius: 12px;
      border: none;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
    }

    input[type="submit"] {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    input[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    input[type="submit"]:active {
      transform: translateY(0);
    }

    .divider {
      height: 1px;
      background: #e2e8f0;
      margin: 32px 0;
    }

    .sample-section {
      text-align: center;
    }

    .sample-section h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      color: #2d3748;
      margin-bottom: 16px;
      font-weight: 700;
    }

    .sample-btn {
      background: #f7fafc;
      color: #667eea;
      border: 2px solid #667eea;
      padding: 12px 24px;
      margin: 0 auto;
      display: inline-block;
      font-weight: 700;
      transition: all 0.3s ease;
    }

    .sample-btn:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }

    @media (max-width: 600px) {
      .container {
        padding: 30px 20px;
      }

      h1 {
        font-size: 28px;
      }

      .number-inputs {
        grid-template-columns: 1fr;
      }

      .button-group {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <div class="logo-icon">📚</div>
        <h1>StudyBuddy</h1>
        <p class="subtitle">Smart Quiz Generator</p>
        <p class="note">Transform your notes into interactive quizzes in seconds</p>
      </div>

      <form action="process.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label>📝 Paste Your Notes</label>
          <textarea name="notes" placeholder="Paste your lecture notes, study material, or any text here..."></textarea>
          <p class="form-hint">Or upload a PDF or text file below</p>
        </div>

        <div class="form-group">
          <label>📎 Upload File (Optional)</label>
          <input type="file" name="notes_file" accept=".pdf,.txt">
          <p class="form-hint">Supports PDF and TXT files up to 10MB</p>
        </div>

        <div class="form-group">
          <label>⚙️ Quiz Settings</label>
          <div class="number-inputs">
            <div>
              <label style="font-size: 13px; margin-bottom: 6px;">Questions per topic</label>
              <input type="number" name="q_per_topic" min="1" max="10" value="3">
            </div>
            <div>
              <label style="font-size: 13px; margin-bottom: 6px;">Number of topics</label>
              <input type="number" name="topics" min="1" max="8" value="2">
            </div>
          </div>
        </div>

        <div class="button-group">
          <input type="submit" value="✨ Generate Quiz">
        </div>
      </form>

      <div class="divider"></div>

      <div class="sample-section">
        <h3>Try a Sample Quiz</h3>
        <button class="sample-btn" onclick="loadSample()">Load Sample Notes</button>
      </div>
    </div>
  </div>

  <script>
    function sampleText() {
      return "Photosynthesis is the process used by plants to convert light energy into chemical energy. Chlorophyll is the green pigment in plants that absorbs light. The light-dependent reactions occur in the thylakoid membranes. The Calvin cycle produces glucose from carbon dioxide. Cell respiration occurs in mitochondria and releases energy. ATP is the energy currency of cells. Enzymes speed up chemical reactions by lowering activation energy. DNA carries genetic information. Protein synthesis involves transcription and translation.";
    }

    function loadSample() {
      document.querySelector('textarea').value = sampleText();
      document.querySelector('textarea').focus();
    }
  </script>
</body>
</html>