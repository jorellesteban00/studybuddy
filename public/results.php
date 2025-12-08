<?php
// results.php
// Receives the POST from process.php (review_payload) and displays a full review page.

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/functions.php';

// Read payload
$payloadRaw = $_POST['review_payload'] ?? '';

if (empty($payloadRaw)) {
    echo "No review data received. Go back and take the quiz.";
    exit;
}

// decode safe
$jsonStr = urldecode($payloadRaw);
$data = json_decode($jsonStr, true);

if (!is_array($data) || !isset($data['review'])) {
    echo "Invalid review data.";
    exit;
}

$total = intval($data['total'] ?? count($data['review']));
$correct = intval($data['correct'] ?? 0);
$review = $data['review'];
$percent = $total > 0 ? round(($correct / $total) * 100) : 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>StudyBuddy — Results Review</title>
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

    .summary {
      display: flex;
      gap: 24px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 32px;
      padding: 24px;
      border-radius: 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      color: white;
      flex-wrap: wrap;
    }

    .summary-item {
      flex: 1;
      min-width: 200px;
    }

    .score-display {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .percent-display {
      font-size: 18px;
      font-weight: 600;
      opacity: 0.95;
    }

    .timestamp-label {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 600;
      margin-bottom: 8px;
    }

    .timestamp {
      font-size: 14px;
      opacity: 0.95;
    }

    .result-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 12px;
      font-weight: 700;
      text-align: center;
    }

    .badge-excellent {
      background: #c6f6d5;
      color: #22543d;
    }

    .badge-good {
      background: #bee3f8;
      color: #1e3a8a;
    }

    .badge-fair {
      background: #feebc8;
      color: #7c2d12;
    }

    .badge-needs-work {
      background: #fed7d7;
      color: #742a2a;
    }

    .question-review {
      margin-bottom: 20px;
      border-radius: 14px;
      padding: 20px;
      background: #f7fafc;
      border-left: 5px solid #667eea;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
    }

    .question-review:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .question-review.correct {
      border-left-color: #48bb78;
    }

    .question-review.wrong {
      border-left-color: #f56565;
    }

    .q-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .q-icon {
      font-size: 20px;
    }

    .q-title {
      font-weight: 700;
      font-size: 15px;
      color: #2d3748;
    }

    .options-section {
      margin-top: 14px;
      margin-bottom: 16px;
    }

    .options-label {
      font-weight: 600;
      font-size: 13px;
      color: #718096;
      margin-bottom: 8px;
      display: block;
    }

    .opt-list {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .opt-item {
      padding: 8px 12px;
      border-radius: 8px;
      background: white;
      border: 2px solid #e2e8f0;
      font-size: 13px;
      color: #4a5568;
      transition: all 0.3s ease;
    }

    .answer-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-top: 16px;
    }

    @media (max-width: 600px) {
      .answer-row {
        grid-template-columns: 1fr;
      }
    }

    .answer-section {
      display: flex;
      flex-direction: column;
    }

    .answer-label {
      font-weight: 700;
      font-size: 13px;
      color: #718096;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .user-answer {
      padding: 12px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 14px;
      border: 2px solid;
      transition: all 0.3s ease;
    }

    .user-answer.correct {
      background: #c6f6d5;
      color: #22543d;
      border-color: #48bb78;
    }

    .user-answer.wrong {
      background: #fed7d7;
      color: #742a2a;
      border-color: #f56565;
    }

    .user-answer.not-answered {
      background: #f0f4ff;
      color: #3b3a8f;
      border-color: #cbd5e0;
    }

    .correct-answer {
      padding: 12px;
      border-radius: 10px;
      background: #edf2f7;
      color: #2d3748;
      border: 2px solid #667eea;
      font-weight: 600;
      font-size: 14px;
    }

    .controls {
      margin-top: 40px;
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 24px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      display: inline-block;
      transition: all 0.3s ease;
      font-size: 15px;
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
    }

    .btn.primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    .btn.primary:active {
      transform: translateY(0);
    }

    .btn.secondary {
      background: #f0f4ff;
      color: #667eea;
      border: 2px solid #667eea;
    }

    .btn.secondary:hover {
      background: #667eea;
      color: white;
      transform: translateY(-2px);
    }

    .divider {
      height: 1px;
      background: #e2e8f0;
      margin: 32px 0;
    }

    @media (max-width: 600px) {
      .container {
        padding: 24px;
      }

      h1 {
        font-size: 24px;
      }

      .summary {
        flex-direction: column;
        align-items: flex-start;
      }

      .summary-item {
        width: 100%;
      }

      .controls {
        flex-direction: column;
      }

      .btn {
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
      <p class="subtitle">Results Review</p>
    </div>

    <div class="summary">
      <div class="summary-item">
        <div class="score-display">Score: <?php echo $correct; ?> / <?php echo $total; ?></div>
        <div class="percent-display"><?php echo $percent; ?>%</div>
        <?php
          $badgeClass = 'badge-excellent';
          $badgeText = '🎉 Perfect!';
          if ($percent < 100 && $percent >= 80) {
            $badgeClass = 'badge-good';
            $badgeText = '🌟 Great Job!';
          } elseif ($percent < 80 && $percent >= 60) {
            $badgeClass = 'badge-fair';
            $badgeText = '👍 Good Effort!';
          } elseif ($percent < 60) {
            $badgeClass = 'badge-needs-work';
            $badgeText = '💪 Keep Learning!';
          }
        ?>
        <div style="margin-top: 12px;">
          <span class="result-badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
        </div>
      </div>

      <div class="summary-item">
        <div class="timestamp-label">📅 Completed At</div>
        <div class="timestamp"><?php echo htmlspecialchars($data['timestamp'] ?? date('Y-m-d H:i:s')); ?></div>
      </div>
    </div>

    <div class="divider"></div>

    <?php foreach ($review as $i => $r): 
        $qn = intval($i) + 1;
        $isCorrect = !empty($r['isCorrect']);
        $userAns = $r['userAnswer'] ?? null;
        $correctAns = $r['correctAnswer'] ?? '';
        $questionText = $r['question'] ?? '';
        $options = is_array($r['options']) ? $r['options'] : [];
    ?>
      <div class="question-review <?php echo $isCorrect ? 'correct' : 'wrong'; ?>">
        <div class="q-header">
          <div class="q-icon"><?php echo $isCorrect ? '✓' : '✗'; ?></div>
          <div class="q-title">Question <?php echo $qn; ?> — <?php echo htmlspecialchars($questionText); ?></div>
        </div>

        <?php if (!empty($options)): ?>
          <div class="options-section">
            <span class="options-label">Available Options:</span>
            <div class="opt-list">
              <?php foreach ($options as $opt): ?>
                <div class="opt-item"><?php echo htmlspecialchars($opt); ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="answer-row">
          <div class="answer-section">
            <div class="answer-label">Your Answer</div>
            <?php if ($userAns === null || $userAns === ''): ?>
              <div class="user-answer not-answered">⚠️ No answer selected</div>
            <?php else: ?>
              <div class="user-answer <?php echo $isCorrect ? 'correct' : 'wrong'; ?>">
                <?php echo $isCorrect ? '✓' : '✗'; ?> <?php echo htmlspecialchars($userAns); ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="answer-section">
            <div class="answer-label">Correct Answer</div>
            <div class="correct-answer">
              ✓ <?php echo htmlspecialchars($correctAns); ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="controls">
      <a href="index.php" class="btn secondary">← Back to Generator</a>
      <a href="index.php" class="btn primary">✨ Take Another Quiz</a>
    </div>
  </div>
</body>
</html>