# PHP implementation of the [Monkey language](https://monkeylang.org/)

Inspired from [Writing An Interpreter In Go](https://interpreterbook.com/) and [Writing A Compiler In Go](https://compilerbook.com/). High quality books if you want to know how an interpreter and compiler work.

**Warning:** implementation for learning purpose only (not ready for production).

## Play with it

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

## Additional features of this implementation

- PHP null safety implementation (use of the Rust-inspired "Option").
- Handle left and right associativity.
- Handle token position (line and column) for debug.
- New expression tokens:
    - `**`: power (with positive exponent)
- Handle postfix expression:
    - `!`: factorial
- Handle escape for double quotes in strings.
- Semicolon mandatory except for an expression at the end of a block.
- Variable can not be redefined with let. Re-assign without let (for example: `let x = 1; x = 2;`).
- Builtin can not be redefined.
- No null System Object : using a Unit Object. Not implemented but the must-have : implement Option like Rust.
- More constraints on operands types.
- Replace "puts" function by "echo" function.

## Quality tools

Help:

```sh
make help
```

### Unit tests

Tips: to see the json structure more clearly, copy the json and paste it onto https://omute.net/editor.
