<?php
// Lightweight CV parser: supports plain text, basic docx extraction and PDF via pdftotext if available.
function extract_text_from_docx(string $path) : string {
    $text = '';
    if (!class_exists('ZipArchive')) return $text;
    $zip = new ZipArchive;
    if ($zip->open($path) === true) {
        if (($idx = $zip->locateName('word/document.xml')) !== false) {
            $xml = $zip->getFromIndex($idx);
            // strip xml tags
            $xml = preg_replace('/<[^>]+>/', ' ', $xml);
            $text = trim(preg_replace('/\s+/', ' ', $xml));
        }
        $zip->close();
    }
    return $text;
}

function extract_text_from_pdf(string $path) : string {
    // Prefer using smalot/pdfparser if installed via composer
    if (class_exists('Smalot\\PdfParser\\Parser')) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
            return $text ?: '';
        } catch (Exception $e) {
            // fallback to pdftotext
        }
    }

    // try pdftotext binary if available
    $out = '';
    $cmd = 'pdftotext ' . escapeshellarg($path) . ' -';
    $res = @shell_exec($cmd);
    if ($res) return $res;
    // fallback: return empty (can't reliably extract)
    return $out;
}

function parse_cv_file(string $path) : array {
    $result = [
        'text' => '',
        'emails' => [],
        'phones' => [],
        'names' => [],
        'skills' => []
    ];

    if (!file_exists($path)) return $result;

    $mime = mime_content_type($path) ?: '';
    $text = '';
    if (strpos($mime, 'pdf') !== false || strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
        $text = extract_text_from_pdf($path);
    } elseif (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['docx'])) {
        $text = extract_text_from_docx($path);
    } else {
        $text = file_get_contents($path);
    }

    $result['text'] = $text;

    // emails
    if (preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $m)) {
        $result['emails'] = array_unique($m[0]);
    }

    // phones: simple pattern
    if (preg_match_all('/(\+?\d[\d\s\-()]{6,}\d)/', $text, $m2)) {
        $result['phones'] = array_unique(array_map(function($p){ return trim($p); }, $m2[0]));
    }

    // try to get probable name: look for explicit labels (Prénom, Nom) or common header patterns
    if (preg_match('/Pr[eé]nom[:\s]+([A-ZÀ-Ÿ][a-zà-ÿ\-\']+)\s+Nom[:\s]+([A-ZÀ-Ÿ][a-zà-ÿ\-\']+)/i', $text, $mn)) {
        $result['names'][] = trim($mn[1] . ' ' . $mn[2]);
    } else {
        // look for 'Nom' and 'Prénom' in any order
        if (preg_match('/Nom[:\s]+([A-ZÀ-Ÿ][a-zà-ÿ\-\']+).*Pr[eé]nom[:\s]+([A-ZÀ-Ÿ][a-zà-ÿ\-\']+)/is', $text, $mn2)) {
            $result['names'][] = trim($mn2[2] . ' ' . $mn2[1]);
        } else {
            // try a header heuristic: pick first 1-3 words that look like a name from the top 4 lines
            $lines = preg_split('/[\r\n]+/', trim($text));
            $searchLines = array_slice($lines, 0, 4);
            foreach ($searchLines as $ln) {
                $ln = trim($ln);
                if (strlen($ln) < 2) continue;
                // ignore emails/phones
                if (filter_var($ln, FILTER_VALIDATE_EMAIL)) continue;
                if (preg_match('/\d{2,}/', $ln)) continue;
                if (preg_match_all('/\b[A-ZÀ-Ÿ][a-zà-ÿ\-\']+\b/u', $ln, $nm)) {
                    $cand = array_slice($nm[0], 0, 3);
                    if (count($cand) >= 1) {
                        $nameCandidate = implode(' ', $cand);
                        // small sanity: must not be a single common word like 'Curriculum' or 'CV'
                        if (!preg_match('/^(CV|Curriculum|Résumé|Resume|Profil)$/i', $nameCandidate)) {
                            $result['names'][] = $nameCandidate;
                            break;
                        }
                    }
                }
            }
        }
    }

    // skills: larger keyword list
    $keywords = [
        'php','javascript','html','css','sql','mysql','postgres','mariadb','mongodb','redis',
        'laravel','symfony','zend','codeigniter','react','vue','angular','svelte','node','express',
        'python','django','flask','java','spring','ruby','rails','go','golang','c#','dotnet','dotnetcore',
        'docker','kubernetes','k8s','aws','azure','gcp','google cloud','heroku','serverless',
        'git','github','gitlab','bitbucket','ci/cd','jenkins','circleci','travis','terraform',
        'agile','scrum','kanban','rest','graphql','api','microservices','tdd','unit test','selenium',
        'nlp','data','machine learning','ml','pandas','numpy','tensorflow','pytorch'
    ];
    $found = [];
    $lower = strtolower($text);
    foreach ($keywords as $k) {
        if (strpos($lower, $k) !== false) $found[] = $k;
    }
    $result['skills'] = array_values(array_unique($found));

    return $result;
}
