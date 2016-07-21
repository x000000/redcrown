<table class="table table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Имя</th>
			<th>Статус</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?= htmlspecialchars($user['id']); ?></td>
			<td><?= htmlspecialchars($user['name']); ?></td>
			<td>
				<?= htmlspecialchars($from); ?>
				-&gt;
				<?= htmlspecialchars($user['status']); ?>
			</td>
		</tr>
	</tbody>
</table>