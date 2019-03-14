<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2017-04-18
 * Time: 13:53
 */

namespace ZRDN;

/**
 * GH
 * This file adds TrHelper class to allow for translations in WordPress format (using __ and _n functions).
 * It's wholly taken from the Twig Extension i18n (http://twig-extensions.readthedocs.io/en/latest/i18n.html)
 * namely, Twig_Extensions_Extension_I18n.
 *
 * I couldn't use Twig_Extensions_Extension_I18n as is because trans, etc. tags are translated to gettext and ngettext
 * which don't seem to work with WordPress since they don't take a domain argument.
 *
 * The domain argument has been hardcoded in DEFAULT_DOMAIN const and is passed to __ and _n functions so WordPress
 * can translate them properly.
 *
 * TrHelper class is used in class.ziprecipes.util.php where twig templates are rendered and bin/twig-gettext-extractor
 * where strings are extracted from twig templates. twig-gettext-extractor is called from the gulp task generate-pot.
 */


/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a trans node.
 *
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */

const DEFAULT_DOMAIN = 'zip-recipes';

class ZRDN_Twig_Extensions_Node_Trans extends \Twig_Node
{
    public function __construct(\Twig_Node $body, \Twig_Node $plural = null, \Twig_Node_Expression $count = null, \Twig_Node $notes = null, $lineno, $tag = null)
    {
        parent::__construct(array('count' => $count, 'body' => $body, 'plural' => $plural, 'notes' => $notes), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        list($msg, $vars) = $this->compileString($this->getNode('body'));

        if (null !== $this->getNode('plural')) {
            list($msg1, $vars1) = $this->compileString($this->getNode('plural'));

            $vars = array_merge($vars, $vars1);
        }

        $function = null === $this->getNode('plural') ? '__' : '_n';

        if (null !== $notes = $this->getNode('notes')) {
            $message = trim($notes->getAttribute('data'));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(array("\n", "\r"), ' ', $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler
                ->write('echo strtr('.$function.'(')
                ->subcompile($msg)
            ;

            if (null !== $this->getNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')')
                ;
            }

            $compiler->raw(', "' . DEFAULT_DOMAIN . '"), array(');

            foreach ($vars as $var) {
                if ('count' === $var->getAttribute('name')) {
                    $compiler
                        ->string('%count%')
                        ->raw(' => abs(')
                        ->subcompile($this->getNode('count'))
                        ->raw('), ')
                    ;
                } else {
                    $compiler
                        ->string('%'.$var->getAttribute('name').'%')
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ')
                    ;
                }
            }

            $compiler->raw("));\n");
        } else {
            $compiler
                ->write('echo '.$function.'(')
                ->subcompile($msg)
            ;

            if (null !== $this->getNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')')
                ;
            }

            $compiler->raw(", '" . DEFAULT_DOMAIN . "');\n");
        }
    }

    /**
     * @param \Twig_Node $body A Twig_Node instance
     *
     * @return array
     */
    protected function compileString(\Twig_Node $body)
    {
        if ($body instanceof \Twig_Node_Expression_Name || $body instanceof \Twig_Node_Expression_Constant || $body instanceof \Twig_Node_Expression_TempName) {
            return array($body, array());
        }

        $vars = array();
        if (count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if (get_class($node) === 'Twig_Node' && $node->getNode(0) instanceof \Twig_Node_SetTemp) {
                    $node = $node->getNode(1);
                }

                if ($node instanceof \Twig_Node_Print) {
                    $n = $node->getNode('expr');
                    while ($n instanceof \Twig_Node_Expression_Filter) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute('name'));
                    $vars[] = new \Twig_Node_Expression_Name($n->getAttribute('name'), $n->getLine());
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return array(new \Twig_Node(array(new \Twig_Node_Expression_Constant(trim($msg), $body->getLine()))), $vars);
    }
}

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ZRDN_Twig_Extensions_TokenParser_Trans extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_Node A Twig_Node instance
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $notes = null;

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            $body = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse(array($this, 'decideForFork'));
            $next = $stream->next()->getValue();

            if ('plural' === $next) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse(array($this, 'decideForFork'));

                if ('notes' === $stream->next()->getValue()) {
                    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                    $notes = $this->parser->subparse(array($this, 'decideForEnd'), true);
                }
            } elseif ('notes' === $next) {
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                $notes = $this->parser->subparse(array($this, 'decideForEnd'), true);
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $this->checkTransString($body, $lineno);

        return new ZRDN_Twig_Extensions_Node_Trans($body, $plural, $count, $notes, $lineno, $this->getTag());
    }

    public function decideForFork(\Twig_Token $token)
    {
        return $token->test(array('plural', 'notes', 'endtrans'));
    }

    public function decideForEnd(\Twig_Token $token)
    {
        return $token->test('endtrans');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     *
     * @return string
     */
    public function getTag()
    {
        return 'trans';
    }

    protected function checkTransString(\Twig_Node $body, $lineno)
    {
        foreach ($body as $i => $node) {
            if (
                $node instanceof \Twig_Node_Text
                ||
                ($node instanceof \Twig_Node_Print && $node->getNode('expr') instanceof \Twig_Node_Expression_Name)
            ) {
                continue;
            }

            throw new \Twig_Error_Syntax(sprintf('The text to be translated with "trans" can only contain references to simple variables'), $lineno);
        }
    }
}


class TrHelper extends \Twig_Extension
{
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(new ZRDN_Twig_Extensions_TokenParser_Trans());
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', 'gettext'),
        );
    }

    public function getName()
    {
        return 'TrHelper';
    }
}