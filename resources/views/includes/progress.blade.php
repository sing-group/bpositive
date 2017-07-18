<?php

/*
 * B+
 * Copyright (C) 2017 Jorge Vieira, José Sousa, Miguel Reboiro-Jato,
 * Noé Vázquez, Bárbara Amorim, Cristina P. Vieira, André Torres, Hugo
 * López-Fernández, and Florentino Fdez-Riverola
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

?>

<div class="modal fade" id="modalProgressBar" tabindex="-1" role="dialog" aria-labelledby="myModalProgressBar">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h3>Progress information</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="control-label">Creating project, please be patient...</label>
                </div>
                <div id="myProgress" class="progress active">
                    <div id="myBar" class="progress-bar progress-bar-striped"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
