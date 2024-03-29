<?php

namespace Scraper\Kernel\Crawler;

/**
* chrome-php
* @link https://github.com/chrome-php/headless-chromium-php
*/

use Scraper\Kernel\Interfaces\Crawler as CrawlerInterface;
use HeadlessChromium\BrowserFactory;
/**
 * @link https://github.com/chrome-php/headless-chromium-php
 */
class Crawler implements CrawlerInterface {
    /**
     * Vars
     */
    private $config;
    private $url;
    private $browserFactory;


    /**
     * Initiate Crawl
     * Configs
     */
    public function __construct(string $url) {
        if (empty($url)) {
            die('Parameter passed null.');
        }

        $this->url = $url;
        $this->browserFactory = new BrowserFactory();
    }
    



    /**
     * Start Crawling
     * @return String
     */
    public function crawl(string $file):string {
        echo "> crawling [$this->url]" . NL; 
        
        $start_time = time();
        
        // starts headless chrome
        $browser = $this->browserFactory->createBrowser(OPTIONS);

        // creates a new page and navigate to an url
        $page = $browser->createPage();

        // Wait for browser timeout to load
        $page->navigate($this->url)->waitForNavigation('networkIdle', TIMEOUT_SEC);

        // Evaluate html
        $evaluate = $page->evaluate('document.documentElement.outerHTML');

        // Get return value
        $html = $evaluate->getReturnValue();

        /**
         * If file has error on saving,
         * rerun and save
         */
        $is_saved = saveToFile($html, $file);
        if (!$is_saved) $this->crawl($file); 
        
        // Close browser
        $browser->close();
        
        echo '> crawled in ' . date('i:s', time() - $start_time) . ' sec' . NL;

        return $html;
    }
}