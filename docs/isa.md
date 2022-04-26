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

- mulb reg8, reg8
  Multiply 8-bit source register value with 8-bit destination register value and put
  its result to destination register.

- divb #<imm8>, reg8
  Divide 8-bit immediate value with 8-bit destination register value and put
  its result to destination register.

- divb reg8, reg8
  Divide 8-bit source register value with 8-bit destination register value and put
  its result to destination register.

- movb #<imm8>, reg8
  Move immediate 8-bit value to destination register.

- movb reg8, reg8
  Move 8-bit source register value to destination register.

- cmpb #<imm8>, reg8
  Compare 8-bit immediate value with 8-bit destination register value. The result
  of the comparation will set the proper status flag, respectively.

- cmpb reg8, reg8
  Compare 8-bit source register value with 8-bit destination register value. The result
  of the comparation will be set the proper status flag, respectively.

- prib reg8
  Print 8-bit source register value to teletype (TTY) console.

- prib #<imm8>
  Print 8-bit immediate value to teletype (TTY) console.

- jmp @<label>
  Jump to specified label.

- jnz @<label>
  Jump to specified label (ZF = 0)

- jz @<label>
  Jump to specified label (ZF = 1)

- halt
  Terminating current running process.
```
