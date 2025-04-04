# Interpreter & Compiler Architecture

See [diagram.mmd](./diagram.mmd).

## Key stages

| Stage | Role |
|---|---|
| **Lexer** | Converts source into a token stream (implements `Iterator`) |
| **Parser** | Pratt parser builds the AST with correct operator precedence via binding powers |
| **Macro Expansion** | Interpreter path only. Two-pass: prune macro definitions from AST, then walk and replace macro calls using a `Modifier` visitor; arguments wrapped as `QuoteSysObject` |
| **Evaluator** | Tree-walks the expanded AST; routes each node type to a dedicated `SubEval` handler; scope managed by `Context` / `Env` (closure chain) |
| **Compiler** | Rule-based bytecode emitter; tracks symbols (`Global`, `Local`, `Builtin`, `Free`) in a `SymbolTable`; free variables are captured and emitted as `Closure` opcodes; literals collected in a constants pool |
| **ByteCode** | Immutable structure: `instructions: Byte[]` + `constants: SysObject[]` |
| **VM** | Stack-based execution loop (one rule per opcode); `Stack` for operands; `Globals` for top-level bindings; `CallStack` of `Frame`s — the first frame holds the top-level program instructions (no closure), subsequent frames are created on function calls and carry the `ClosureSysObject` for free variable access |

Both execution paths produce `SysObject` values as their result.
