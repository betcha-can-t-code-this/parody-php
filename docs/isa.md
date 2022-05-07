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

- orb #<imm8>, r8
  Perform bitwise or-ing 8-bit immediate value with 8-bit destination register value and
  put its result to destination register.

- orb r8, r8
  Perform bitwise or-ing 8-bit source register value with 8-bit destination register value and
  put its result to destination register.

- xorb #<imm8>, r8
  Perform bitwise xor-ing 8-bit immediate value with 8-bit destination register value and
  put its result to destination register.

- xorb r8, r8
  Perform bitwise xor-ing 8-bit source register value with 8-bit destination register value and
  put its result to destination register.

- prib reg8
  Print 8-bit source register value to teletype (TTY) console.

- prib #<imm8>
  Print 8-bit immediate value to teletype (TTY) console.

- jmp @<label>
  Jump (near) to specified label.

- jne @<label>
  Jump to specified label (ZF = 0)

- je @<label>
  Jump to specified label (ZF = 1)

- jnz @<label>
  Jump to specified label (ZF = 0)

- jz @<label>
  Jump to specified label (ZF = 1)

- jg @<label>
  Jump to specified label (GF = 1)

- jge @<label>
  Jump to specified label (GF = 1 or ZF = 1)

- jl @<label>
  Jump to specified label (LF = 1)

- jle @<label>
  Jump to specified label (LF = 1 or ZF = 1)

- halt
  Terminate current running process.
```
