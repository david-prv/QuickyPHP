# Code style and Formatting

We want QuickyPHP to stay easily readable and extensible. To ensure this, we use a bunch of
static analysis tools which help us to improve the code quality. Any proposed pull request has
to fullfill the following requirements:

### 1. Use PHPDocs for every method
We follow the standards, enforced by PHPStan. [Learn more](https://phpstan.org/writing-php-code/phpdocs-basics)

### 2. Use proper Formatting
- 4 spaces indent
- `elseif` instead of `else if`
- spaces before conditional parenthesises:
```php
if (condition) {
    // some code...
} 

for ($i = 0; $i < 1000; $i++) {
    // loop code...
}

while (condition) {
    // loop code...
}

// And so on. You got the idea, right?
```
- function brackets to the next line:
```php
// good!
function myTestFunction(string $message): void
{
    // do something...
}

// bad!
function myTestFunction(string $message): void {
    // do something
}
```
- for php8 support, always add typing to parameters
- also always add return-types (see code example above)
- all files end with an empty line
- use camel-case for naming functions, parameters and properties:
```php
// good!
function longMethodNameIWantToUse(string $longParamNameIUseToo): void;
private string $userName;

// bad!
function longmethodnameiwanttouse(string $long_param_name_i_use_too): void;
private string $user_name;
```
- avoid in-line commands:
```php
// good!
if (condition) {
    return something;
}

// bad!
if (condition) return something;
```
- follow the PSR-4 standard
- name your classes as follows, iff they belong to the corresponding type:
```php
// interfaces
interface SomeNameInterface;

// middleware
class SomeNameMiddleware implements MiddlewareInterface;

// managers
class SomeNameManager implements ManagingInterface;

// exceptions
class SomeNameException extends SomeParentException;
```
You may use PHP_CodeSniffer, since it already enforces most of the formatting rules above.
Learn how to use it [here](https://github.com/squizlabs/PHP_CodeSniffer).

### 3. Pass PHPStan Level 0 Analysis
PHPStan already enforces most of the code style rules above. Your code has to pass it. 
```bash
./vendor/bin/phpstan analyze app
```
Should return:
```
$ ./vendor/bin/phpstan analyze app

32/32 [============================] 100%
                                                                                                          
 [OK] No errors                                                                                                         
                                                                                                                        
```
### 4. Pass PHPMD static analysis
Git will run a PHPMD codesize check for every commit. Your code has to pass it.  
```bash
./vendor/bin/phpmd app text codesize
./vendor/bin/phpmd app text controversial
```
Iff it returns nothing, your code has no issues.


