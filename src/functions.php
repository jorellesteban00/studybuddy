<?php
namespace StudyBuddy;

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;

class functions
{
    public function generateFromText(string $text, int $k = 2, int $qPerTopic = 3): array
    {
        $sentences = $this->splitSentences($text);

        if (count($sentences) < 3) {
            $sentences = array_filter(array_map('trim', explode(PHP_EOL, $text)));
        }

        $documents = array_values($sentences);

        $vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
        $vectorizer->fit($documents);
        $vectorizer->transform($documents);

        $tfidf = new TfIdfTransformer($documents);
        $tfidf->transform($documents);

        $labels = $this->assignLabels($documents, $k);

        $grouped = [];
        foreach ($labels as $i => $lbl) {
            $grouped[$lbl][] = $sentences[$i];
        }

        $topics = [];
        foreach ($grouped as $grp) {
            $keywords = $this->extractKeywords($grp, 6);
            $questions = $this->buildQuestions($grp, $keywords, $qPerTopic);

            $topics[] = [
                "keywords" => $keywords,
                "questions" => $questions
            ];
        }

        return ["topics" => $topics];
    }

    private function splitSentences(string $text): array
    {
        $parts = preg_split('/(?<=[.?!])\s+/m', trim($text));
        return array_filter(array_map('trim', $parts));
    }

    private function extractKeywords(array $sentences, int $limit): array
    {
        $words = strtolower(implode(' ', $sentences));
        $words = preg_replace('/[^a-z0-9\s]/', ' ', $words);
        $parts = preg_split('/\s+/', $words);

        $stop = ['the','and','for','that','this','these','those','with','from','are','was','were','has','have','had','but','not','you','your','their','they','which','also','into','can','may','will','been','such','what','when','how','where','use','used','using'];

        $freq = [];
        foreach ($parts as $w) {
            if (strlen($w) < 3) continue;
            if (in_array($w, $stop)) continue;

            $freq[$w] = ($freq[$w] ?? 0) + 1;
        }

        arsort($freq);
        return array_slice(array_keys($freq), 0, $limit);
    }

    private function buildQuestions(array $sentences, array $keywords, int $limit): array
    {
        $out = [];
        $used = [];
        $usedSentences = []; // ← NEW: Track sentences already used

        foreach ($sentences as $s) {
            foreach ($keywords as $kw) {

                if (stripos($s, $kw) !== false 
                    && !in_array($kw, $used)
                    && !in_array($s, $usedSentences)) {

                    $out[] = $this->makeQuestion($s, $kw, $keywords);
                    $used[] = $kw;
                    $usedSentences[] = $s; // ← Mark sentence as used
                }

                if (count($out) >= $limit) break;
            }
            if (count($out) >= $limit) break;
        }

        // Synthetic questions if needed
        while (count($out) < $limit) {
            foreach ($keywords as $kw) {
                if (!in_array($kw, $used)) {
                    $out[] = $this->synthQuestion($kw, $keywords);
                    $used[] = $kw;
                }
                if (count($out) >= $limit) break;
            }
        }

        // FINAL duplicate cleanup
        $unique = [];
        $filtered = [];

        foreach ($out as $q) {
            if (!in_array($q["question"], $unique)) {
                $unique[] = $q["question"];
                $filtered[] = $q;
            }
        }

        return array_slice($filtered, 0, $limit);
    }

    private function makeQuestion(string $sentence, string $keyword, array $keywords): array
    {
        $masked = preg_replace('/\b' . preg_quote($keyword, '/') . '\b/i', "___", $sentence, 1);
        $distractors = $this->distractors($keyword, $keywords);
        $opts = array_merge($distractors, [$keyword]);
        shuffle($opts);

        return [
            "question" => ucfirst($masked),
            "options" => $opts,
            "answer" => $keyword
        ];
    }

    private function synthQuestion(string $keyword, array $keywords): array
    {
        $opts = $this->distractors($keyword, $keywords);
        $opts[] = $keyword;
        shuffle($opts);

        return [
            "question" => "Which of the following relates to the term \"$keyword\"?",
            "options" => $opts,
            "answer" => $keyword
        ];
    }

    private function distractors($correct, $keywords): array
    {
        $list = array_filter($keywords, fn($k) => $k !== $correct);
        shuffle($list);
        $out = array_slice($list, 0, 3);

        while (count($out) < 3) {
            $out[] = "energy";
        }
        return $out;
    }

    private function assignLabels(array $samples, int $k): array
    {
        $labels = [];
        foreach ($samples as $i => $vec) {
            $sum = array_sum($vec);
            $labels[$i] = ($sum + $i) % $k;
        }
        return $labels;
    }
}