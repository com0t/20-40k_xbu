<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Lazyload extends Ressio_Plugin
{
    static public $blankImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    static public $blankIframe = 'about:blank';

    private $numImages = 0;
    private $numIframes = 0;

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);
        sort($params->srcsetwidth);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIMG($event, $optimizer, $node)
    {
        if ($this->params->image) {
            $this->numImages++;
            if ($this->numImages > $this->params->skipimages) {
                if ($this->params->srcset && $this->params->addsrcset && !$node->hasAttribute('srcset')) {
                    // @todo refactor: generation of srcset attribute is not responsibility of the lazy-loading plugin
                    $this->createSrcset($node);
                }
                $this->lazyfyNode($node, $optimizer);
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagVIDEO($event, $optimizer, $node)
    {
        if ($this->params->video) {
            if ($node->hasAttribute('ress-nolazy')) {
                $node->removeAttribute('ress-nolazy');
                return;
            }
            if ($optimizer->nodeIsDetached($node) || $optimizer->isNoscriptState()) {
                return;
            }

            $modified = false;

            if ($node->hasAttribute('src') && !$node->hasAttribute('data-src')) {
                $src = $node->getAttribute('src');
                if (strncmp($src, 'data:', 5) === 0 && !preg_match('/(?:__|\}\})$/', $src)) {
                    $modified = true;
                    $node->setAttribute('data-src', $src);
                    $node->removeAttribute('src');
                }
            }

            if ($node->hasAttribute('poster') && !$node->hasAttribute('data-poster')) {
                $src = $node->getAttribute('poster');
                if (strncmp($src, 'data:', 5) === 0 && !preg_match('/(?:__|\}\})$/', $src)) {
                    $modified = true;
                    $node->setAttribute('data-poster', $src);
                    $node->removeAttribute('poster');
                    // @todo Optimize poster image
                }
            }

            if ($modified) {
                // @todo Add <noscript> verswion
                $node->addClass('lazy');
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIFRAME($event, $optimizer, $node)
    {
        // @todo Exclude list for iframes
        if ($this->params->video || $this->params->iframe) {
            $this->numIframes++;
            if ($this->numIframes > $this->params->skipiframes) {
                $this->lazyfyNode($node, $optimizer);
            }
        }
    }

    /**
     * @param $node IRessio_HtmlNode
     * @param $optimizer IRessio_HtmlOptimizer
     */
    private function lazyfyNode($node, $optimizer)
    {
        if ($node->hasAttribute('ress-nolazy')) {
            $node->removeAttribute('ress-nolazy');
            return;
        }

        if ($node->hasAttribute('onload') || $node->hasAttribute('onerror')) {
            return;
        }

        if ($optimizer->nodeIsDetached($node) || $optimizer->isNoscriptState() ||
            !$node->hasAttribute('src') ||
            strncmp($node->getAttribute('src'), 'data:', 5) === 0
        ) {
            return;
        }

        if ($node->hasAttribute('width') && $node->hasAttribute('height')
            && $node->getAttribute('width') === '1' && $node->getAttribute('height') === '1') {
            return;
        }

        // skip data attributes (sliders, etc.)
        if ($node instanceof DOMElement) {
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attr) {
                    if (strncmp($attr->nodeName       /  if (strncmp($attr   ame       rO?ef (strncmp($attr   ame  )
   rOameode->removeAttribute('ress-noltmp($,yastimef (strncmp($att$s/om!=null){ar.margin    IRp($    /om!=null       /ctn    IRp($    /om!=null       /ctneo  IRp(s(h/m    ,yasize/om!=null       $s/r.marginRight=this.>reru/ute('width') === '1' && $node->getAttriB    ='widt'1' && $nome g?eiinRight=thiwme g?u' &r.miome g?eiinRigriBthiwme  /cd=thiwme g?u' &r.miome g?e  /ctneo  IRp($node->attributd ($no/mwep($node->atmap/mw}/ddLegendRowHooks[ake  /ctnrlugin
{
    static public $blaod =' ame       rO?efd    r(                 if (strncmp($attr->nod      owHooks[ake  /ctnrlugin
{ddLep(         }

            $modified eoDl8)-)a &r.mi<T' &Nees<T' &NL:his._elem.css("left", Drncmp($attr->nod      owte('width') === '1' && $node->       }
ex3L>nod    NL:his._e mIe<T'  )-)a &r.md)s\his._elem.css("left", Drncmp($attr->nod      owteNL:)-)e   Eons.heiga    mp($=thde)
=me o;od      a &r./grl     if (sr./grl     ifis) {
      0<ouMld>npouwn}$i=th?h('/(?:__|npouwn}$i=th?h('jd6>N2?iIietAttribute('width') === '1' && $node->getAttribute('heigode->getAttribute('heigode->getAt?4><?D!dLe[M{
  eeft+(this._plotDimensions.width-aj.right))/2-this.rRl$i=th?h('  /ctt,Ilribute('onele g?u('  /ct/ntoe->getAttrtdeoDl8)-)a &r.mi<T' &erix?e/Lribute('ws->params->skipif?sl=null}}}return this/dth-aAttribute('heigode-e.rRl$i=thaAttrmsmizer)
  s)-)ae mIe<T, '1' && $node->getAttri/m    ,yasize/om!=nullai/m ssgtimef (strn)terateTagIe->geoiae mIe<T, '1' && $no -.butd ($no/mwep($node->== (:         $this->te('$a
   ix('$a
   ixt>params->skipht  zsp($oe2& $not        strn<ioagIe->geoiaerRlleoiaerRlleoiaerRlle("lebute('$not        strn<irgendRowHMessio_HtmlNode
     e}}}return this/dth-aAttribute('heigode-e.rRl$i=thaAttrmsmize            }

       tmlOptimizer
     * @ptmp($,tera
   $paraft+(thisncmp($attdRowHMessiFuggendRowHMesHtmlOptimizer
     *zerTgpublic $blaoL:)-wHMesHtmlOpti.en
   $par=Kcgpub;_endRowHMesHtmlOptiD &erix?e/Lribute('w/fublic 'Td6>N      }

       tmlOptimizer
     * @ptmp(pue x?e/Lribute('w/fublic 'Td6>N      }

       tmlOptimizer
     * -o/fua   * -o/fua   * -o,/fua   * -o,/fuat phattr->nod c>t phattr->noncmp($attdRowHMessiF t! us>no Drncmp($aic:i/drt<h);a       pMesHtmlOptimizer
     *zerTgpublic $blaoL:)-wHMesHtmlOpti.en
   $par=Kcgpub;_endRowHMesHtmlOptiD &erix?e/Lribute('w/fublic 'Td6>N      }

       tmlOptimizer
     * iD ($optimizer->nodeIsD(ta aserved.
 * 
    ($oic $blaoL:)-wHMesHtmlOpti.en
 gEgibutes (slesHtmlOpti.en
   $par= si.en
;this._elem.css({right:ai iD (bi iD (bi iD (bi iD (bi i.e.rRlate('w/fublic 'Td6>N     bBI nu($attdaoL:)-wHMesHtmlOpti.en
 gEgibutes (slesHtmlOpti.en
   $par= si.en
;this._elem.css({right:ai iD (bi iD (bi iD (bi iD (bi i.e.rRlate('w/fublic 'Td6>N     bBI nu($attdaoL:)-wHMesHtmlO'Td6>N      }

  /e('w/fublic 'Td6>N r
     * -o/(Sl][0]($att$esr, $n$atwePaRl$i=thaAttrmsmizPaRl$i   $ nui

 erM2?iIietAttribute('width') === '1' Ee\ode-GoI   rOameode-Rl$i=thaAttrmsmizP>GoI   rOameode-Rl$i=thaAttrmsmidlOpt,Optites/mth') === g"_el(tp(   r') === '1' Ee\ode-GoI   rAttrmsmizP)rncmp( !ytrue;
                    $/deoDlretAttribute('=d atwePaRl$i=thaAttrmsmizPaRl$i O-SSIO Responsive Se iD em.css({right:ai iD (bi iD (bi 
esponsive Se iD e eeL=gaRl
            }

            $modified = fndifi|           }

            $modified = fndifi|           }

            $modified = fndifi|           }

            $modified = fndifi|           }

            $modified = fndifi|            * @ptmp(pue x?eDcesH"ed = fndifi|           }

            $modsmizP)rncmiogc0

            $modsmizP    * @parnimodsA/   * @parD8(Souwn}$iP|sdth') === '1' :c
           $/deoDlretAttributP|srNndiisrh

tmlOpti.en
 gEgibu0'rNndiisrh

tmlOpti.en
 gEgibu0'rNndiisrh

tmleslPmodsmizP)rncmiogc0

          ilOpe           $modified =eud/vl    MmlOpti this/dth    $/deo(;/vl    MmlOpsrNndiisrh

tmlOpti.en
 gEgibu         $/deoDlretAo,Egibu         $/deoDlretAoee=El=gaRl
            }

    oode->getAt?zP)rncmp(hsHtmlOI(Egib'sndifi|           }

            $modifie}

    oode->getAt?z   $modifie}Opt          $modifs    uC    $node->removeAttributeiup C    $node->$ifie}

   "moveAttributeiup C    $nodetisrh

tmlOpti.en
 gEgibu         $/deoDlretAo,Egibuode
cd =eud/vl    MmlOpti_-wHMesHtmlO'Td6>N      }

  /e('w/fubli}S>N    ,    /**
     * @param $node movye Se iD em.css({right:ai  (?D em.css({right:ai  }ueiup * @uest(0i=ciAth }
$e$uy  iD m   b* @uest(0i=ciAth }
$e$uy  lOpsrNndiisrh
up y.s') === '1' EeHeD * @uest(0i=ciAth }
$e$uy  iurn thisisMmlOe  lOpsrNndiisrh
up y.s') === /or2mlOp/Hiuode
cd =eud/vl    MmlOpti_-w=cisru hc

  /e('w/fubli}S>N    ,    /**
     * @param $node movye S         }s._plotDimensions.width-aj.right))/2-this.rRl$i=th?h('  /ctt,Ilribute('onele g?u('  /ct/ntoe->getAttrtdeoDl8)-)a &r.mi<T' &erix?e/Lribute('ws->params->skipif?sl=null}}}(i}S>N    ,    /**
     * @param $node movye S      