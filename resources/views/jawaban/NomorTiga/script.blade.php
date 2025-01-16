<script>
	$(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('.table-schedule').DataTable({
			language: {
				paginate: {
					next: '<i class="bi bi-arrow-right"></i>',
					previous: '<i class="bi bi-arrow-left"></i>'
				},
				emptyTable: "Data tidak ditemukan",
			}
		});

	$(document).on('click', '.edit-btn', function(event) {
		event.preventDefault();
		const id = $(this).data('id');
		console.log('Edit button pressed, ID:', id);

		$.ajax({
			url: "{{ route('event.getSelected') }}",
			method: 'POST',
			data: { id: id },
			dataType: 'json',
			success: function(response) {
				console.log('Response received:', response);
				if (response.status && response.data) {
					$('#editForm')[0].reset();

					$('#edit_id').val(response.data.id);
					$('#edit_name').val(response.data.name);
					$('#edit_start').val(response.data.start);
					$('#edit_end').val(response.data.end);

					const modal = new bootstrap.Modal(document.getElementById('editModal'));
					modal.show();
				} else {
					console.log('Invalid response:', response);
					alert('Data is not valid');
				}
			},
			error: function(xhr) {
				console.error('Error fetching data:', xhr.responseText);
				alert('An error occurred while retrieving data');
			}
		});
	});

	$(document).on('submit', '#editForm', function(event) {
		event.preventDefault();
		console.log('Submitting form');

		const formData = $(this).serialize();
		console.log('Serialized data:', formData);

		$.ajax({
			url: "{{ route('event.update') }}",
			method: 'POST',
			data: formData,
			dataType: 'json',
			beforeSend: function() {
				$('#editForm button[type="submit"]').prop('disabled', true);
				console.log('Processing update...');
			},
			success: function(response) {
				console.log('Update response:', response);
				if (response.status) {
					console.log('Update successful');
					$('#editModal').modal('hide');
					setTimeout(() => location.reload(), 500);
				} else {
					console.log('Update failed:', response.message);
					alert(response.message || 'Failed to save changes');
				}
			},
			error: function(xhr) {
				console.error('Error during update:', xhr.responseText);
				alert('Failed to save changes');
			},
			complete: function() {
				$('#editForm button[type="submit"]').prop('disabled', false);
				console.log('Update completed');
			}
		});
	});

	$(document).on('click', '.delete-btn', function(event) {
		event.preventDefault();
		const id = $(this).data('id');

		if (confirm('Are you sure you want to delete this schedule?')) {
			$.ajax({
				url: "{{ route('event.delete') }}",
				method: 'POST',
				data: { id: id },
				dataType: 'json',
				success: function(response) {
					if (response.status) {
						location.reload();
					} else {
						alert(response.message || 'Failed to delete schedule');
					}
				},
				error: function(xhr) {
					console.error('Error during deletion:', xhr.responseText);
					alert('Failed to delete schedule');
				}
			});
		}
	});

</script>