### Instruction Set Architecture.

```
- addb #<imm8>, reg8
  Add 8-bit immediate value with 8-bit destination register value and put
  its result to destination register.

- addb reg8, reg8
  Add 8-bit source register value with 8-bit destination register value and put
  its result to destination register.

- subb #<imm8>, reg8
  Subtract 8-bit immediate value with 8-bit destination register value and put
  its result to destination register.

- subb reg8, reg8
  Subtract 8-bit source register value with 8-bit destination register value and put
  its result to destination register.

- mulb #<imm8>, reg8
  Multiply 8-bit immediate value with 8-bit destination register value and put
  its result to destination register.

- divb #<imm8>, reg8
  Divide 8-bit immediate value with 8-bit destination register value and put
  its result to destination register.

- movb #<imm8>, reg8
  Move immediate 8-bit value to destination register.

- movb reg8, reg8
  Move 8-bit source register value to destination register.

- prib reg8
  Print 8-bit source register value to teletype (TTY) console.

- prib #<imm8>
  Print 8-bit immediate value to teletype (TTY) console.

- jmp @<label>
  Jump to specified label.
```