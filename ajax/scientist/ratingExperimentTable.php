<table class="table bordered hovered">
	<thead>
		<tr class="header-list">
			<th>

			</th>
			<?php
			/**
			 * A simple php file to write out data in a given format.
			 */
			$dataSet = $_POST['data'];

			foreach($dataSet as $data) {
				echo "<th>".$data['name']."</th>";
			}

			?>
		</tr>
	</thead>

	<tbody class="result-list">


	</tbody>
</table>
