# NOTES

## Pomůcky

### Regex pro nahrazení objektového přístupu za array přístup

        ->(\w+)\)
        ['$1'])

## Regulární výrazy

### Postprocess ProblemFinal Body

#### FIRST: RE to remove expressions in brackets multiplied by zero

    (note: actually only for \big( ... \big) expressions - needs to be extended)

    [\+\-]?\s*0\s*\\big\([\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\\big\)

#### SECOND: RE to remove fractions multiplied by zero

    [\+\-]?\s*0\s*\\frac\{[\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}\s*\{[\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}

#### THIRD: RE to remove values multiplied by zero

    [\+\-]\s*0(\s+|\s*\*\s*)\d

#### FINALLY: RE to remove zero values

    [\+\-]\s*0\s*
    
## TESTING TEMPLATES

### Testing Linear Equations templates

    $$ x + <par min="-5" max="15"/> = 4 $$
    
    $$ <par min="1" max="3"/> x = <par min="1" max="2"/> $$
    
    $$ <par min="-3" max="3"/> x = <par min="-2" max="2"/> - <par min="-3" max="3"/>x $$
    
    $$ <par min="-5" max="5"/> x - <par min="-2" max="2"/> = <par min="-5" max="5"/> - <par min="-4" max="4"/>x $$
    
    $$ \frac{<par min="-3" max="3"/>x}{<par min="-3" max="3"/>} + 5 = \frac{<par min="-3" max="3"/>}{2} - <par min="-5" max="5"/> $$
    
    $$ \frac{<par min="-2" max="2"/>x}{<par min="-3" max="3"/>} + \frac{<par min="-3" max="3"/>x}{<par min="-3" max="3"/>} = <par min="-3" max="3"/> $$
    
    $$ x + <par min="0" max="0"/> \big( 5 x + 4 \big) + <par min="0" max="0"/> = - <par min="0" max="0"/> $$
    
    $$ 5 x = 15 + <par min="-5" max="5"/> + 2 $$
    
    $$ <par min="-5" max="6"/> x + <par min="-5" max="5"/> = <par min="-5" max="5"/> $$
    
    $$ \frac{<par min="-5" max="6"/> x + <par min="-5" max="5"/>}{5} = <par min="-5" max="5"/> $$

### Testing Quadratic Equations templates with variable fractions

    $$ <par min="-5" max="5"/> x^2 + x + 5 = 4 $$
    
    $$ <par min="-5" max="5"/> x^2 + <par min="-5" max="5"/>x + <par min="-5" max="5"/> = 0 $$
    
    $$ \frac{x}{x - <par min="-5" max="5"/>} + \frac{x - <par min="-5" max="5"/>}{6} = \frac{4}{2} $$
    
    $$ \frac{x - <par min="-5" max="5"/>}{x} + \frac{x}{x - 1} + \frac{<par min="-10" max="10"/>}{x - x^2} = 0 $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{x^2 + x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{2 x^2 + x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{3 x^2 + 3 x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{3 x + 3 x} + \frac{<par min="-5" max="5"/>}{x + <par min="-5" max="5"/>} $$
    
    <!-- + 4 will shorten with 4 <par ... -->
    $$ 1 = \frac{x - <par min="-5" max="5"/> + 4}{ 4 <par min="-3" max="3"/> \big( 3 x + 3 \big) } + \frac{<par min="-4" max="4"/>}{x + <par min="-4" max="4"/>} $$
    
    $$ \frac{<par min="-3" max="3"/>}{x - <par min="-3" max="3"/>} - \frac{1}{x + 2} + \frac{8}{x - 4} = 0 $$
    
    $$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> \big( x - 2 \big) } - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$
    
    $$ \frac{<par min="-2" max="2"/> + 5}{ \big( x + 2 \big) \big( x - <par min="-2" max="2"/> \big) } - \frac{1}{x + 2} + \frac{2}{4} = 0 $$

### Testing Quadratic Equations in multiply form

    $$ <par min="-2" max="2"/> \big( 4x - <par min="-3" max="3"/> \big) \big( x + 1 \big) = \big( <par min="-4" max="4"/> + 1 \big) \big( x - 1 \big) - 7$$

### Testing Arithmetic Sequences

    $$ a_n = <par min="-5" max="5"/> $$
    
    $$ a_n = <par min="-5" max="5"/> n $$
    
    $$ a_n = <par min="-5" max="5"/> - <par min="-5" max="5"/> n $$
    
    $$ a_n = \frac{<par min="-5" max="5"/> n - <par min="-5" max="5"/>}{<par min="-5" max="5"/>} $$
    
    $$ a_n = \frac{<par min="-5" max="5"/> - <par min="-5" max="5"/>}{3} $$

### Testing Geometric Sequences

    $$ q_n = <par min="-5" max="5"/> $$
    
    $$ q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{n-1} $$
    
    $$ q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{1-n} $$
    
    $$ q_n = <par min="-5" max="5"/> * 3^{1-n} $$
    
    $$ q_n = \big( - <par min="-5" max="5"/> \big)^{n} $$

### Testing postprocessProblemFinalExpression

    $$ \frac{ -2 }{x - 2} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$

## Database

### problem_prototype_json_data

    ALTER TABLE prototype_json_data ADD UNIQUE condition_problem_unique(condition_type_id, problem_id);
    
    ALTER TABLE prototype_json_data DROP INDEX condition_problem_unique;


## Command Line

### Doctrine

-   Vytvoření DB schématu:

        sudo php ./www/index.php orm:schema-tool:create

-   Drop schématu:

        sudo php ./www/index.php orm:schema-tool:drop --force

-   Update schématu:

        sudo php ./www/index.php orm:schema-tool:update --force

### PHPUnit

    ./vendor/bin/phpunit --bootstrap App/bootstrap.php App/AppTests/Model/Entity

### Known problems

-   Timeout - parameters complexity threshhold
-   Database longtext max length - threshhold

### Newton API restrictions

-   Variable can't be "e" --> it's parsed as exp() function !!!: OK
-   Variable for equations can be only x, y or z (Newton API then formats output into equation standard form)
    -   Solve by select box with variables x, y and z for equations, for others templates types, leave text input: OK
    
-   equations in fractions format can't be handled by Newton API --> in needs to be manually multiplied by variable dividers!!!   
    -   NEEDS TO BE SOLVED!!!
    -   PROPOSAL
        -   Simplify expression (in classic way)
        -   Search for fractions with variable in divider
        -   If those were found, get all the dividers containing variable
        -   Find all the fractions dividers: \/\s*(\([x\-\+\s\(\)\d]*\))
        -   Find all the fractions (with grouped counters and dividers): ([x\d\sp]*)\/\s*(\([\-\+\s\(\)\dx]*\))
        -   Multiply all the expression members with collected dividers (make fraction divider 1, multiply counter by remaining dividers)
            -   Torn found variable fractions from expression, process it, then multiply rest of the expression with all the dividers and merge both parts
        
        
        Detect zero multiplied bracket:    
        0\s?(\(([\sx\+\d\(]+\)+))+