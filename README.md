# String finder

Простая библиотека для поиска текста в файле.

## Install

composer require engelcss/text-finder

## Docs

1. Set up composer autoload:<br/>
`require_once __DIR__ . '/vendor/autoload.php';`
2. Use class:<br/>
`use TextFinder\Validator;`
3. Create new instance:<br/>
`$finder = new Finder('What I search', 'file/for/searching.txt');`
4. Output array result:<br/>
`print_r($finder->toArray());`
5. Output result into string:<br/>
`echo($finder);`

## License

The MIT License (MIT). Please see [License File](https://github.com/engel/founder/blob/master/LICENSE) for more information.
