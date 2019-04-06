<?php
/*
 * This file is part of the TwigStack extension for Twig
 *
 * (c) 2014 Arno Geurts
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */

namespace TwigStack\Extension;

use TwigStack\NodeVisitor\StackNodeVisitor;
use TwigStack\TokenParser\StackPopTokenParser;
use TwigStack\TokenParser\StackPushTokenParser;
use Core\Stack\CollectionInterface as StacksInterface;

/**
 * The Twig extension, which should be added to the Twig environment to enable TwigStack
 *
 * @package TwigStack\Extension
 * @author Arno Geurts
 */
class StackExtension extends \Twig_Extension
{
    private $stacks;

    public function __construct(StacksInterface $stacks)
    {
        $this->stacks = $stacks;
    }

    /**
     * Push the given content to the stack identified by its name
     * If the stack does not exist, create it
     *
     * @param string $stackName
     * @param string $content
     */
    public function pushStack($stackName, $content)
    {
        $this->stacks->pull($stackName)->push($content);
    }

    /**
     * Render the stacks in the output string
     *
     * @param string $output
     * @return string
     */
    public function render($output)
    {
        // try to find the following string in the output
        // stack_pop_[stashName]([seperator])
        $regex = '/stack\_pop\_([\w]*)\(([^\)]*)\)/';
        $callback = function ($matches) {
            return $this->stacks->pull($stackName)->output($matches[2]);
        };
        return preg_replace_callback($regex, $callback, $output);
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(
            new StackPushTokenParser(),
            new StackPopTokenParser()
        );
    }

    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return \Twig_NodeVisitorInterface[] An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array(
            new StackNodeVisitor()
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    /*public function getName()
    {
        return 'stack';
    }*/
}

class_alias('TwigStack\Extension\StackExtension', 'Twig_StackExtension');
