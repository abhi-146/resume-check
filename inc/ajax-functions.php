<?php
/*
* Handle ajax calls
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
   exit;
}


// Resume compatibility check
add_action( 'wp_ajax_rsai_resume_check', 'rsai_resume_check' );
add_action( 'wp_ajax_nopriv_rsai_resume_check', 'rsai_resume_check' );

if (!function_exists('rsai_resume_check')) {
	function rsai_resume_check(){
		$res_array = array();
		$urlText = '';
		$pdfText = '';

		// Verify nonce for security
		if (!isset($_POST['rsai_nonce']) || !wp_verify_nonce($_POST['rsai_nonce'], 'rsai_ajax_nonce')) {

			$res_array['error'] = __('Error - Could not verify nonce', 'your-text-domain');
			echo json_encode($res_array);
			exit;
		}

		// Check if files and URL are present
		if (!isset($_FILES['pdfFile']) || !isset($_POST['sourceUrl'])) {

			$res_array['error'] = __('Error - Missing PDF file or URL.', 'your-text-domain');
			echo json_encode($res_array);
			exit;
		}

		$url = sanitize_text_field($_POST['sourceUrl']);

		$file = $_FILES["pdfFile"]["tmp_name"];
		global $rsai_plugin_url;

		require_once rsai_PLUGIN_DIR . 'vendor/autoload.php';


		// Initialize and load PDF Parser library
		$parser = new \Smalot\PdfParser\Parser();

		$pdf = $parser->parseFile($file);
		$pdfText = $pdf->getText();


		if (!filter_var($url, FILTER_VALIDATE_URL) === false) {

			$content = file_get_contents($url);
			$doc = new DOMDocument();
			libxml_use_internal_errors(true);
			$doc->loadHTML($content);
			libxml_clear_errors();

			$body = $doc->getElementsByTagName("body")->item(0);
			$bodyContent = $doc->saveHTML($body);
			$bodyContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $bodyContent);
			$bodyContent = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $bodyContent);
			$urlText = $bodyContent;

		}

		$res_array['score'] = checkCompatibility($pdfText, $urlText);

		echo json_encode($res_array);
		exit;
	}
}

    // Advanced text processing to extract keywords
     function advancedTextProcessing($text) {
        $words = preg_split('/\W+/', strtolower($text));
        $keywords = array_filter($words, function($word) {
            return !empty($word) && !isStopWord($word);
        });
        return array_values($keywords);
    }

    // Calculate the importance of each keyword
     function calculateKeywordImportance($keywords) {
        $importanceMap = [];
        // Define your lists of programming languages and tools
        $programmingLanguages = [ 'java', 'python', 'c++', 'javascript', 'ruby', 'kotlin', 'scala', 'swift',
		'typescript', 'php', 'c#', 'go', 'perl', 'rust', 'r', 'matlab', 'sql',
		'groovy', 'lua', 'dart', 'objective-c', 'shell', 'powershell', 'bash',
		'erlang', 'elixir', 'fortran', 'cobol', 'vb.net', 'haskell', 'assembly',
		'vba', 'awk', 'f#'];
		$tools = [
			'maven', 'gradle', 'docker', 'kubernetes', 'jenkins', 'git', 'github',
			'gitlab', 'svn', 'ansible', 'puppet', 'chef', 'terraform', 'vscode',
			'intellij', 'eclipse', 'netbeans', 'jira', 'trello', 'slack', 'aws',
			'azure', 'gcp', 'nginx', 'apache', 'tomcat', 'webpack', 'babel', 'npm',
			'yarn', 'vagrant', 'circleci', 'travis', 'bitbucket', 'jupyter', 'tableau',
			'selenium', 'postman', 'datadog', 'grafana', 'new relic', 'splunk', 'docker-compose',
			'prometheus', 'grafana', 'jupyter', 'tensorflow', 'keras', 'pytorch', 'scikit-learn',
			'hadoop', 'spark', 'kafka', 'flink', 'rabbitmq', 'nginx', 'express', 'flask', 'django',
			'spring', 'laravel', 'react', 'angular', 'vue', 'svelte', 'flutter', 'react-native'
		];

        foreach ($keywords as $keyword) {
            if (in_array($keyword, $programmingLanguages)) {
                $importanceMap[$keyword] = 20.0;
            } elseif (in_array($keyword, $tools)) {
                $importanceMap[$keyword] = 15.0;
            } else {
                $importanceMap[$keyword] = 1.0;
            }
        }
        return $importanceMap;
    }

    // Calculating match score
     function calculateMatchScore($resumeKeywords, $jobDescKeywords, $importanceMap) {
        $matchCount = 0;
        foreach ($resumeKeywords as $word) {
            if (in_array($word, $jobDescKeywords)) {
                $matchCount += $importanceMap[$word] ?? 1.0;
            }
        }
        return $matchCount / count($resumeKeywords);
    }

    // Check for stop words
     function isStopWord($word) {
        $stopWords = ["and", "the", "of", "to", "a", "in", "for", "is", "on", "that", "by", "this", "with", "i", "you", "it", "not", "or", "be", "are", "from", "at", "as", "your", "all", "have", "new", "more", "an", "was", "we", "will", "home", "can", "us", "about", "if", "page", "my", "has", "search", "free", "but", "our", "one", "other", "do", "no", "information", "time", "they", "site", "he", "up", "may", "what", "which", "their", "news", "out", "use", "any", "there", "see", "only", "so", "his", "when", "contact", "here", "business", "who", "web", "also", "now", "help", "get", "pm", "view", "online", "first", "am", "been", "would", "how", "were", "me", "s"];
        return in_array($word, $stopWords);
    }

    // Public method to check compatibility
    function checkCompatibility($resumeText, $jobDescription) {
        $resumeKeywords = advancedTextProcessing($resumeText);
        $jobDescKeywords = advancedTextProcessing($jobDescription);

        $keywordImportanceMap = calculateKeywordImportance($jobDescKeywords);

        $matchScore = calculateMatchScore($resumeKeywords, $jobDescKeywords, $keywordImportanceMap);
        return sprintf("Compatibility Index: %.2f%%", $matchScore * 100);
    }
