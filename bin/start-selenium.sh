#!/bin/bash

# A script to start selenium browser for browser testing with behat

vendor/bin/selenium-server-standalone -p 4444 -Dwebdriver.chrome.driver="./vendor/bin/chromedriver"
