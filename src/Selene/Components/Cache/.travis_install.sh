#!/bin/sh

version=`php -v|grep -o "5\.[0-9]"`;

if [ $version == '5.4' ]
	then 
		curl -o APC-3.1.10.tgz http://pecl.php.net/get/APC-3.1.10.tgz
		tar -xzf APC-3.1.10.tgz
		sh -c "cd APC-3.1.10 && phpize && ./configure && make && sudo make install && cd .."
		rm -Rf APC-3.1.10
		rm APC-3.1.10.tgz
		echo "extension=apc.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
		echo "apc.enable_cli=On" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
		phpenv rehash    
fi