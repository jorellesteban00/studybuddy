<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/functions.php';

use StudyBuddy\functions;

// Handle file upload
$notes = $_POST['notes'] ?? '';

if (isset($_FILES['notes_file']) && $_FILES['notes_file']['size'] > 0) {
    $file_ext = pathinfo($_FILES['notes_file']['name'], PATHINFO_EXTENSION);
    
    if ($file_ext === 'txt') {
        $notes = file_get_contents($_FILES['notes_file']['tmp_name']);
    } elseif ($file_ext === 'pdf') {
        require_once __DIR__ . '/../vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($_FILES['notes_file']['tmp_name']);
        $notes = $pdf->getText();
    }
}

$qPerTopic = $_POST['q_per_topic'] ?? 3;
$topics = $_POST['topics'] ?? 2;

if (empty($notes)) {
    echo "No notes provided. Go back and paste notes or upload a file.";
    exit;
}

$generator = new functions();
$quiz = $generator->generateFromText($notes, $topics, $qPerTopic);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>StudyBuddy — Interactive Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
      padding: 30px 20px;
      min-height: 100vh;
      color: #2d3748;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      border-radius: 20px;
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
      font-size: 40px;
      margin-bottom: 12px;
    }

    h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 32px;
      font-weight: 800;
      color: #2d3748;
      margin-bottom: 8px;
    }

    .subtitle {
      color: #718096;
      font-size: 16px;
      font-weight: 500;
    }

    .scorebox {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 24px;
      border-radius: 16px;
      margin-bottom: 32px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      display: none;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .score {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .feedback {
      font-size: 16px;
      opacity: 0.95;
    }

    .topic-section {
      margin-bottom: 36px;
    }

    .topic-title {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 700;
      color: #667eea;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid #e2e8f0;
    }

    .question {
      background: #f7fafc;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 16px;
      border-left: 5px solid #667eea;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
    }

    .question:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .question-text {
      font-weight: 600;
      font-size: 15px;
      margin-bottom: 14px;
      color: #2d3748;
    }

    .choices {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    @media (min-width: 768px) {
      .choices {
        grid-template-columns: 1fr 1fr;
      }
    }

    .choice-card {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 12px 14px;
      border-radius: 10px;
      background: white;
      border: 2px solid #e2e8f0;
      cursor: pointer;
      transition: all 0.3s ease;
      user-select: none;
    }

    .choice-card:hover {
      border-color: #667eea;
      background: #f0f4ff;
      transform: translateX(4px);
    }

    .choice-card input[type="radio"] {
      appearance: none;
      width: 20px;
      height: 20px;
      border: 2px solid #cbd5e0;
      border-radius: 50%;
      cursor: pointer;
      margin-top: 2px;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    .choice-card input[type="radio"]:hover {
      border-color: #667eea;
    }

    .choice-card input[type="radio"]:checked {
      background: #667eea;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .letter {
      min-width: 26px;
      font-weight: 700;
      color: #667eea;
      font-size: 14px;
    }

    .choice-text {
      font-size: 14px;
      color: #2d3748;
      line-height: 1.4;
    }

    .result {
      margin-top: 12px;
      padding: 10px 12px;
      border-radius: 8px;
      display: none;
      font-weight: 600;
      font-size: 14px;
    }

    .result.correct {
      background: #c6f6d5;
      color: #22543d;
      border-left: 4px solid #48bb78;
    }

    .result.wrong {
      background: #fed7d7;
      color: #742a2a;
      border-left: 4px solid #f56565;
    }

    .reveal-info {
      margin-top: 12px;
      padding: 10px 12px;
      border-radius: 8px;
      background: #edf2f7;
      color: #2d3748;
      font-size: 13px;
      display: none;
      border-left: 4px solid #667eea;
    }

    .reveal-correct {
      font-weight: 700;
      color: #667eea;
    }

    .controls {
      display: flex;
      gap: 12px;
      margin-top: 32px;
      flex-wrap: wrap;
      align-items: center;
    }

    button {
      padding: 12px 24px;
      border-radius: 10px;
      border: none;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
      font-size: 15px;
    }

    #submitBtn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    #submitBtn:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    #submitBtn:active {
      transform: translateY(0);
    }

    #resetBtn {
      background: #f0f4ff;
      color: #667eea;
      border: 2px solid #667eea;
    }

    #resetBtn:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }

    .back-link {
      margin-left: auto;
      text-decoration: none;
      padding: 12px 24px;
      border-radius: 10px;
      background: #f0f4ff;
      color: #667eea;
      font-weight: 700;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .back-link:hover {
      background: #e2e8f0;
      transform: translateY(-2px);
    }

    .small-note {
      color: #718096;
      font-size: 13px;
      margin-top: 16px;
      text-align: center;
    }

    @media (max-width: 600px) {
      .container {
        padding: 24px;
      }

      h1 {
        font-size: 24px;
      }

      .controls {
        flex-direction: column;
      }

      .back-link {
        margin-left: 0;
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo-icon">📚</div>
      <h1>StudyBuddy</h1>
      <p class="subtitle">Interactive Quiz</p>
    </div>

    <!-- Hidden form used to POST the review payload to results.php -->
    <form id="resultsPostForm" action="results.php" method="post" style="display:none;">
      <input type="hidden" name="review_payload" id="review_payload" value="">
    </form>

    <form id="quizForm" onsubmit="return false;">
      <div class="scorebox" id="scorebox">
        <div class="score" id="scoreText">Score: 0 / 0</div>
        <div class="feedback" id="scoreFeedback"></div>
      </div>

      <?php
      $letters = ['A','B','C','D','E','F'];
      $globalQ = 0;
      ?>

      <?php foreach ($quiz['topics'] as $tIndex => $topic): ?>
        <div class="topic-section" data-topic-index="<?php echo $tIndex; ?>">
          <div class="topic-title">📖 Topic <?php echo ($tIndex+1); ?></div>

          <?php foreach ($topic['questions'] as $qIndex => $q): ?>
            <?php
              $globalQ++;
              $questionId = "q_{$tIndex}_{$qIndex}";
              $options = $q['options'];
              $correctText = $q['answer'];
            ?>
            <div class="question" id="card-<?php echo $questionId; ?>"
                 data-correct="<?php echo htmlspecialchars($correctText, ENT_QUOTES); ?>"
                 data-qid="<?php echo $questionId; ?>">
              <div class="question-text"> <?php echo ($qIndex+1) . ". " . htmlspecialchars($q['question']); ?></div>

              <div class="choices" role="radiogroup" aria-labelledby="<?php echo $questionId; ?>">
                <?php foreach ($options as $optIndex => $opt):
                  $inputName = "answer_{$globalQ}";
                  $inputId = "{$questionId}_opt_{$optIndex}";
                ?>
                  <label class="choice-card" for="<?php echo $inputId; ?>">
                    <input type="radio"
                           id="<?php echo $inputId; ?>"
                           name="<?php echo $inputName; ?>"
                           value="<?php echo htmlspecialchars($opt, ENT_QUOTES); ?>">
                    <div class="letter"><?php echo $letters[$optIndex] ?? $letters[count($letters)-1]; ?></div>
                    <div class="choice-text"><?php echo htmlspecialchars($opt); ?></div>
                  </label>
                <?php endforeach; ?>
              </div>

              <div class="result" id="result-<?php echo $questionId; ?>"></div>

              <div class="reveal-info" id="reveal-<?php echo $questionId; ?>">
                <strong>✓ Correct Answer:</strong> <span class="reveal-correct"></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <div class="controls">
        <button id="submitBtn" type="button">✨ Submit Quiz</button>
        <button id="resetBtn" type="button">↻ Reset Answers</button>
        <a href="index.php" class="back-link">← Back to Home</a>
      </div>

      <p class="small-note">After submitting, you will be redirected to the full review page.</p>
    </form>
  </div>

  <script>
    (function(){
      const submitBtn = document.getElementById('submitBtn');
      const resetBtn = document.getElementById('resetBtn');
      const scorebox = document.getElementById('scorebox');
      const scoreText = document.getElementById('scoreText');
      const scoreFeedback = document.getElementById('scoreFeedback');
      const resultsPostForm = document.getElementById('resultsPostForm');
      const reviewPayloadInput = document.getElementById('review_payload');

      const questionCards = Array.from(document.querySelectorAll('.question'));

      function evaluateQuizAndSend() {
        let total = questionCards.length;
        let correctCount = 0;
        const review = [];

        questionCards.forEach((card, idx) => {
          const qid = card.dataset.qid;
          const correctText = card.dataset.correct;
          const questionText = card.querySelector('.question-text')?.textContent?.trim() ?? '';
          const inputs = Array.from(card.querySelectorAll('input[type="radio"]'));

          const selected = inputs.find(i => i.checked);
          let correctIndex = -1;
          inputs.forEach((i, j) => {
            if (i.value.trim().toLowerCase() === String(correctText).trim().toLowerCase()) {
              correctIndex = j;
            }
          });

          const userAnswer = selected ? selected.value : null;
          const isCorrect = selected && (selected.value.trim().toLowerCase() === String(correctText).trim().toLowerCase());
          if (isCorrect) correctCount++;

          const options = inputs.map(i => i.value);
          const correctAnswerText = (correctIndex >= 0 && inputs[correctIndex]) ? inputs[correctIndex].value : correctText;

          review.push({
            qid: qid,
            question: questionText,
            options: options,
            userAnswer: userAnswer,
            correctAnswer: correctAnswerText,
            isCorrect: !!isCorrect
          });

          const resultEl = document.getElementById('result-' + qid);
          const revealEl = document.getElementById('reveal-' + qid);
          const revealCorrectEl = revealEl.querySelector('.reveal-correct');

          resultEl.style.display = 'block';
          revealEl.style.display = 'block';
          revealCorrectEl.textContent = correctAnswerText;

          if (!selected) {
            resultEl.className = 'result wrong';
            resultEl.textContent = '✗ No answer selected';
          } else if (isCorrect) {
            resultEl.className = 'result correct';
            resultEl.textContent = '✓ Correct!';
          } else {
            resultEl.className = 'result wrong';
            resultEl.textContent = '✗ Wrong!';
          }
        });

        scorebox.style.display = 'block';
        scoreText.textContent = `Score: ${correctCount} / ${total}`;
        
        const percentage = Math.round((correctCount / total) * 100);
        if (percentage === 100) {
          scoreFeedback.textContent = '🎉 Perfect! Outstanding work!';
        } else if (percentage >= 80) {
          scoreFeedback.textContent = '🌟 Great job! Keep it up!';
        } else if (percentage >= 60) {
          scoreFeedback.textContent = '👍 Good effort! Review the material.';
        } else {
          scoreFeedback.textContent = '💪 Keep learning! Try again!';
        }

        const payload = {
          total: total,
          correct: correctCount,
          timestamp: new Date().toISOString(),
          review: review
        };

        reviewPayloadInput.value = encodeURIComponent(JSON.stringify(payload));
        resultsPostForm.submit();
      }

      function resetQuiz() {
        questionCards.forEach(card => {
          const inputs = Array.from(card.querySelectorAll('input[type="radio"]'));
          inputs.forEach(i => i.checked = false);

          const resultEl = document.getElementById('result-' + card.dataset.qid);
          const revealEl = document.getElementById('reveal-' + card.dataset.qid);

          if (resultEl) { resultEl.style.display = 'none'; resultEl.textContent = ''; }
          if (revealEl) { revealEl.style.display = 'none'; }
        });

        scorebox.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.style.opacity = 1;
      }

      submitBtn.addEventListener('click', evaluateQuizAndSend);
      resetBtn.addEventListener('click', resetQuiz);

      document.getElementById('quizForm').addEventListener('keydown', function(e){
        if (e.key === 'Enter') {
          e.preventDefault();
          evaluateQuizAndSend();
        }
      });
    })();
  </script>
</body>
</html>