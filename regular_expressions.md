# REGULAR EXPRESSIONS

## Postprocess ProblemFinal Body

### FIRST: RE to remove expressions in brackets multiplied by zero

    (note: actually only for \big( ... \big) expressions - needs to be extended)

    [\+\-]?\s*0\s*\\big\([\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\\big\)

### SECOND: RE to remove fractions multiplied by zero

    [\+\-]?\s*0\s*\\frac\{[\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}\s*\{[\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}

### THIRD: RE to remove values multiplied by zero

    [\+\-]\s*0(\s+|\s*\*\s*)\d

### FINALLY: RE to remove zero values

    [\+\-]\s*0\s*
