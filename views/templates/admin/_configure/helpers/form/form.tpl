{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'controllers_select' && isset($controllers) && !empty($controllers)}

        <div class="col-lg-8">
            <table class="table">
                <tr>
                    <td>
                        <!-- Controllers Available -->
                        <select id="controllers_select_1" class="input-large" multiple>
                            {if isset($controllers.available) && !empty($controllers.available)}
                                {foreach from=$controllers.available key=name item=controller}
                                    <option value="{$name}">{$controller}</option>
                                {/foreach}
                            {/if}
                        </select>
                        <a href="#" id="controllers_select_add" onclick="addControllersSelection(this);" data-action="add" class="btn btn-default btn-block clearfix">
                            {l s='Add' d='Modules.infiniteScrollPrestashop.Actions'}
                            <i class="icon-arrow-right"></i>
                        </a>
                    </td>
                    <td>
                        <!-- Controllers selected -->
                        <select id="controllers_select_2" name="controllers_enabled" class="input-large" multiple>
                            {if isset($controllers.selected) && !empty($controllers.selected)}
                                {foreach from=$controllers.selected key=name item=controller}
                                    <option value="{$name}">{$controller}</option>
                                {/foreach}
                            {/if}
                        </select>
                        <a href="#" id="controllers_select_remove" onclick="removeControllersSelection(this);" data-action="remove" class="btn btn-default btn-block clearfix">
                            <i class="icon-arrow-left"></i>
                            {l s='Remove' d='Module.infiniteScrollPrestashop.Actions'}
                        </a>
                    </td>
                </tr>
            </table>
            <span>{$input.descr}</span>
        </div>

        <script type="text/javascript">
            {literal}

                function removeControllersSelection(item) {
                    const id = $(item).attr('id').replace('_remove', '');
                    $(`#${id}_2 option:selected`).remove().appendTo(`#${id}_1`);
                }

                function addControllersSelection(item) {
                    const id = $(item).attr('id').replace('_add', '');
                    $(`#${id}_1 option:selected`).remove().appendTo(`#${id}_2`);
                }

                $('submitInfiniteScrollPs').submit(function(){
                    $('#controllers_select_2 option').each(function(i){
                        $(this).prop('selected', true);
                    });

                    $('#controllers_select_1 option').each(function(i){
                        $(this).prop('selected', false);
                    });

                });

            {/literal}
        </script>
    {/if}
    {$smarty.block.parent}
{/block}