<?php
/**
 * DokuWiki Default Template 2012
 *
 * @link     http://dokuwiki.org/template
 * @author   Anika Henke <anika@selfthinker.org>
 * @author   Clarence Lee <clarencedglee@gmail.com>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */
header('X-UA-Compatible: IE=edge,chrome=1');

$hasSidebar = page_findnearest($conf['sidebar']);
$showSidebar = $hasSidebar && ($ACT=='show');
?><!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset="utf-8" />
    <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
    <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
    <link rel="stylesheet" href="<?php echo DOKU_BASE;?>lib/editor.md/css/editormd.min.css" />
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
    <?php tpl_includeFile('meta.html') ?>
</head>

<body>    
	<script src="<?php echo DOKU_BASE;?>lib/editor.md/examples/js/jquery.min.js"></script>
    <script src="<?php echo DOKU_BASE;?>lib/editor.md/editormd.js"></script>
	<script type="text/javascript">
        var testEditor;
        $(function(md) {
            testEditor = editormd("editormd", {
                width: "100%",
                height: 740,
        
                path: '<?php echo DOKU_BASE;?>lib/editor.md/lib/',
                theme: "dark",
                /*
                previewTheme : "dark",
                editorTheme : "pastel-on-dark",
                markdown : md,
                */
                codeFold: true,
                syncScrolling: "single",
        
                saveHTMLToTextarea: true, // ���� HTML �� Textarea
                searchReplace: true,
                //watch : false,                                // �ر�ʵʱԤ��
                htmlDecode: "style,script,iframe|on*", // ���� HTML ��ǩ������Ϊ�˰�ȫ�ԣ�Ĭ�ϲ�����
                //toolbar    : false,                         //�رչ�����
                //previewCodeHighlight : false, // �ر�Ԥ�� HTML �Ĵ���������Ĭ�Ͽ���
                emoji: true,
                taskList: true,
                tocm: true, // Using [TOCM]
                tex: true, // ������ѧ��ʽTeX����֧�֣�Ĭ�Ϲر�
                flowChart: true, // ��������ͼ֧�֣�Ĭ�Ϲر�
                sequenceDiagram: true, // ����ʱ��/����ͼ֧�֣�Ĭ�Ϲر�,
                //dialogLockScreen : false,     // ���õ�����Ի���������ȫ��ͨ�ã�Ĭ��Ϊtrue
                //dialogShowMask : false,         // ���õ�����Ի�����ʾ͸�����ֲ㣬ȫ��ͨ�ã�Ĭ��Ϊtrue
                //dialogDraggable : false,        // ���õ�����Ի��򲻿��϶���ȫ��ͨ�ã�Ĭ��Ϊtrue
                //dialogMaskOpacity : 0.4,        // ����͸�����ֲ��͸���ȣ�ȫ��ͨ�ã�Ĭ��ֵΪ0.1
                //dialogMaskBgColor : "#000", // ����͸�����ֲ�ı�����ɫ��ȫ��ͨ�ã�Ĭ��Ϊ#fff
                imageUpload: true,
                imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                imageUploadURL: "<?php echo DOKU_BASE;?>uploadimg.php",
                onload: function() {}
            });
        });
        
        window.onload = function() {
            //document.getElementById("editormd").addEventListener('paste', function (event) {
            $("#editormd").on('paste', function(event) {
                //console.log(event);
                var items = (event.clipboardData || event.originalEvent.clipboardData).items;
                for (var index in items) {
                    var item = items[index];
                    //console.log(item);
                    if (item.kind === 'file') {
                        var blob = item.getAsFile();                        
                        var reader = new FileReader();
                        reader.onload = function(event) {                            
                            var base64 = event.target.result;
                            console.log(base64);
                            //ajax�ϴ�ͼƬ
                            $.post("<?php echo DOKU_BASE;?>uploadimg.php", {
                                screenshots: base64
                            }, function(rets) {
                                ret = JSON.parse(rets);
                                //layer.msg(ret.msg);
                                console.log(ret);
                                if (ret.success === 1) {
                                    //��һ�е�ͼƬ��ʾ
                                    testEditor.insertValue("\n![](" + ret.url + ")");
                                } else {
                                    alert("��ͼ�ϴ�ʧ�ܣ�" + ret.message);
                                }
                            });
                        };
                        reader.readAsDataURL(blob);
                    }
                }
            });
        }
	</script>
    <div id="dokuwiki__site"><div id="dokuwiki__top" class="site <?php echo tpl_classes(); ?> <?php
        echo ($showSidebar) ? 'showSidebar' : ''; ?> <?php echo ($hasSidebar) ? 'hasSidebar' : ''; ?>">

        <?php include('tpl_header.php') ?>

        <div class="wrapper group">

            <?php if($showSidebar): ?>
                <!-- ********** ASIDE ********** -->
                <div id="dokuwiki__aside"><div class="pad aside include group">
                    <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
                    <div class="content"><div class="group">
                        <?php tpl_flush() ?>
                        <?php tpl_includeFile('sidebarheader.html') ?>
                        <?php tpl_include_page($conf['sidebar'], true, true) ?>
                        <?php tpl_includeFile('sidebarfooter.html') ?>
                    </div></div>
                </div></div><!-- /aside -->
            <?php endif; ?>

            <!-- ********** CONTENT ********** -->
            <div id="dokuwiki__content"><div class="pad group">
                <?php html_msgarea() ?>

                <div class="pageId"><span><?php echo hsc($ID) ?></span></div>

                <div class="page group">
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pageheader.html') ?>
                    <!-- wikipage start -->
                    <?php tpl_content() ?>
                    <!-- wikipage stop -->
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div>

                <div class="docInfo"><?php tpl_pageinfo() ?></div>

                <?php tpl_flush() ?>
            </div></div><!-- /content -->

            <hr class="a11y" />

            <!-- PAGE ACTIONS -->
            <div id="dokuwiki__pagetools">
                <h3 class="a11y"><?php echo $lang['page_tools']; ?></h3>
                <div class="tools">
                    <ul>
                        <?php
                            $data = array(
                                'view'  => 'main',
                                'items' => array(
                                    'edit'      => tpl_action('edit',      true, 'li', true, '<span>', '</span>'),
                                    'revert'    => tpl_action('revert',    true, 'li', true, '<span>', '</span>'),
                                    'revisions' => tpl_action('revisions', true, 'li', true, '<span>', '</span>'),
                                    'backlink'  => tpl_action('backlink',  true, 'li', true, '<span>', '</span>'),
                                    'subscribe' => tpl_action('subscribe', true, 'li', true, '<span>', '</span>'),
                                    'top'       => tpl_action('top',       true, 'li', true, '<span>', '</span>')
                                )
                            );

                            // the page tools can be amended through a custom plugin hook
                            $evt = new Doku_Event('TEMPLATE_PAGETOOLS_DISPLAY', $data);
                            if($evt->advise_before()){
                                foreach($evt->data['items'] as $k => $html) echo $html;
                            }
                            $evt->advise_after();
                            unset($data);
                            unset($evt);
                        ?>
                    </ul>
                </div>
            </div>
        </div><!-- /wrapper -->

        <?php include('tpl_footer.php') ?>
    </div></div><!-- /site -->

    <div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
    <div id="screen__mode" class="no"></div><?php /* helper to detect CSS media query in script.js */ ?>
</body>
</html>
