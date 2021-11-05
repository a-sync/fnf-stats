<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (count($items) === 0) :
    echo '<div class="mdc-typography--body1 list__no_items">No ops found...</div>';
else :
    $unique_count = count(array_unique(array_column($items, 'operation_id')));
    echo '<div class="mdc-typography--caption list__total">' . $unique_count . ' ops</div>';

    $show_hit_data = (!defined('ADJUST_HIT_DATA') || ADJUST_HIT_DATA >= 0) ? true : false;
    $event_types = $this->config->item('event_types');
    $sides = $this->config->item('sides');
?>
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 flex--center">
                <div class="mdc-data-table mdc-elevation--z2">
                    <div class="mdc-data-table__table-container">

                        <?php echo $player_menu; ?>

                        <table class="mdc-data-table__table sortable">
                            <thead>
                                <tr class="mdc-data-table__header-row">
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="descending" data-column-id="op_id">ID</th>
                                    <th class="mdc-data-table__header-cell cell__title" role="columnheader" scope="col" aria-sort="none" data-column-id="op_info" title="Event @ Start time (Duration)">
                                        Mission <sup class="mdc-typography--caption">(Map) Winner</sup>
                                    </th>
                                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col" aria-sort="none" data-column-id="group">Group</th>
                                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col" aria-sort="none" data-column-id="role_name" title="Name">Role</th>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="shots" title="Shots">S</th>
                                    <?php if ($show_hit_data) : ?>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="hits" title="Hits">H</th>
                                    <?php endif; ?>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="kills" title="Kills">K</th>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="deaths" title="Deaths">D</th>
                                    <?php if ($show_hit_data) : ?>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="fhits" title="Friendly fire">FF</th>
                                    <?php endif; ?>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="fkills" title="Teamkills">Tk</th>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="vkills" title="Destroyed assets">DA</th>
                                </tr>
                            </thead>
                            <tbody class="mdc-data-table__content">
                                <?php
                                $prev_op_id = 0;
                                $merged_indexes = [];
                                foreach ($items as $index => $i) :
                                    if (in_array($index, $merged_indexes)) {
                                        continue;
                                    }
                                    $duration_min = floor(intval($i['mission_duration']) / 60);

                                    $next_index = $index + 1;
                                    $next_i = isset($items[$next_index]) ? $items[$next_index] : false;
                                    while ($next_i !== false && $i['operation_id'] === $next_i['operation_id']) {
                                        if (
                                            $i['name'] === $next_i['name']
                                            && $i['side'] === $next_i['side']
                                            && $i['role'] === $next_i['role']
                                        ) {
                                            $i['shots'] += $next_i['shots'];
                                            $i['hits'] += $next_i['hits'];
                                            $i['fhits'] += $next_i['fhits'];
                                            $i['kills'] += $next_i['kills'];
                                            $i['fkills'] += $next_i['fkills'];
                                            $i['vkills'] += $next_i['vkills'];
                                            $i['deaths'] += $next_i['deaths'];

                                            $merged_indexes[] = $next_index;
                                        }

                                        $next_index++;
                                        $next_i = isset($items[$next_index]) ? $items[$next_index] : false;
                                    }

                                    $role = $i['role'];
                                    $group = $i['group_name'];
                                    if (strpos($role, '@') !== false) {
                                        $role_group_arr = explode('@', $role);
                                        if (isset($role_group_arr[0]) && $role_group_arr[0] !== '') {
                                            $role = $role_group_arr[0];
                                        }
                                        if (isset($role_group_arr[1]) && $role_group_arr[1] !== '') {
                                            $group = $role_group_arr[1];
                                        }
                                    }

                                    $hits = $i['hits'];
                                    if (defined('ADJUST_HIT_DATA') && $i['operation_id'] < ADJUST_HIT_DATA) {
                                        $hits = '';
                                    }

                                    $fhits = $i['fhits'];
                                    if (defined('ADJUST_HIT_DATA') && $i['operation_id'] < ADJUST_HIT_DATA) {
                                        $fhits = '';
                                    }

                                    $new_op_row = ($prev_op_id !== $i['operation_id']);
                                ?>
                                    <tr class="mdc-data-table__row">
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric">
                                            <a href="<?php echo base_url('op/') . $i['operation_id']; ?>"><?php echo $i['operation_id']; ?></a>
                                        </td>
                                        <td class="mdc-data-table__cell">
                                            <span title="<?php echo $event_types[$i['event']]; ?> @ <?php echo $i['start_time']; ?> (<?php echo $duration_min ?>m)">
                                                <?php echo html_escape($i['mission_name']); ?>&nbsp;<sup class="mdc-typography--caption"><a target="_blank" title="OCAP" href="<?php echo OCAP_URL_PREFIX . urlencode($i['filename']); ?>"><img src="<?php echo base_url('public/ocap_logo.png'); ?>" alt="OCAP" class="ocap-link"></a>
                                                    (<?php echo html_escape($i['world_name']); ?>) <?php echo $sides[$i['end_winner']]; ?>
                                                </sup>
                                            </span>
                                        </td>
                                        <td class="mdc-data-table__cell <?php echo 'side__' . html_escape(strtolower($i['side'])); ?>">
                                            <?php echo $sides[$i['side']]; ?> <?php echo html_escape($group); ?>
                                        </td>
                                        <td class="mdc-data-table__cell">
                                            <span title="<?php echo html_escape($i['name']); ?>"><?php echo html_escape($role); ?></span>
                                        </td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['shots']; ?></td>
                                        <?php if ($show_hit_data) : ?>
                                            <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $hits; ?></td>
                                        <?php endif; ?>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['kills']; ?></td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['deaths']; ?></td>
                                        <?php if ($show_hit_data) : ?>
                                            <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $fhits; ?></td>
                                        <?php endif; ?>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['fkills']; ?></td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['vkills']; ?></td>
                                    </tr>
                                <?php
                                    $prev_op_id = $i['operation_id'];
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>