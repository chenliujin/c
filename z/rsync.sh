#!/bin/bash

rsync --exclude "*.swp" -avz ../mysql /opt/php/lib/php/chenliujin/
rsync --exclude "*.swp" -avz ./model /opt/php/lib/php/z/
