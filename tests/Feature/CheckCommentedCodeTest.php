<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CheckCommentedCodeTest extends TestCase
{
    /**
     * Test to ensure there are no commented-out code blocks in PHP files across the full project.
     */
    public function test_no_commented_out_code()
    {
        // Directories to scan (including modules)
        $directories = [
            app_path(),
            base_path('routes'),
            base_path('database'),
            base_path('Modules'), // Scan modules
        ];

        // Patterns to match commented-out code
        $commentPatterns = [
            '/\/\/\s*\$.*;/',  // Matches single-line commented-out code (// $variable = ...;)
            '/\/\*.*?\*\//s',  // Matches block comments (/* ... */)
        ];

        $flaggedFiles = [];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue; // Skip if directory doesn't exist
            }

            $phpFiles = File::allFiles($directory);

            foreach ($phpFiles as $file) {
                $content = File::get($file);

                foreach ($commentPatterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        $flaggedFiles[] = $file->getPathname();
                    }
                }
            }
        }

        $this->assertEmpty($flaggedFiles, "Commented-out code found in files:\n" . implode("\n", $flaggedFiles));
    }
}
