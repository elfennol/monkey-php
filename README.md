# PHP implementation of the [Monkey language](https://monkeylang.org/)

Inspired from [Writing An Interpreter In Go](https://interpreterbook.com/) and [Writing A Compiler In Go](https://compilerbook.com/). High-quality books if you want to know how an interpreter and compiler work.

**Warning:** implementation for learning purpose only (not ready for production).

## Architecture

See [diagram.md](doc/diagram.md) for a diagram of how the interpreter and compiler work.

## Requirements

- PHP >= 8.5
- Composer

```sh
composer install
```

## Play with it

### Interpreter

Interactive shell:

```sh
php bin/monkey.php
```

From file:

```sh
php bin/monkey.php < file
```

From string:

```sh
echo string | php bin/monkey.php
```

### Compiler

Same usage as the interpreter, using `bin/monkeyc.php` instead of `bin/monkey.php`.

## Additional features of this implementation

- The PHP host code uses a Rust-inspired `Option` type for null safety.
- Handle left and right associativity.
- Handle token position (line and column) for debug.
- New expression tokens:
    - `**`: power (with positive exponent)
- Handle postfix expression:
    - `!`: factorial
- Handle escape for double quotes in strings.
- Semicolon mandatory except for an expression at the end of a block.
- Variable cannot be redefined with let. Re-assign without let (for example: `let x = 1; x = 2;`).
- Builtin cannot be redefined.
- No null value in the Monkey language: uses a `Unit` object instead. Note: a native `Option` type in the Monkey language (à la Rust) is not implemented yet.
- More constraints on operands types.
- Replace the "puts" function with an "echo" function.

## Quality tools

Help:

```sh
make help
```

### Unit tests

Test fixtures are located in `tests/fixtures/` as JSON files. To read them more easily, copy the content and paste it into https://omute.net/editor.
