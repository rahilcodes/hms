<h1>Book Your Stay</h1>

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="/book">
    @csrf

    <label>Room Type</label>
    <select name="room_type_id" required>
        @foreach($roomTypes as $room)
            <option value="{{ $room->id }}">
                {{ $room->name }} (₹{{ $room->base_price }})
            </option>
        @endforeach
    </select>

    <br><br>

    <label>Check-in</label>
    <input type="date" name="check_in" required>

    <br><br>

    <label>Check-out</label>
    <input type="date" name="check_out" required>

    <br><br>

    <label>Rooms</label>
    <input type="number" name="rooms" min="1" value="1" required>

    <br><br>

    <label>Name</label>
    <input type="text" name="name" required>

    <br><br>

    <label>Phone</label>
    <input type="text" name="phone" required>

    <br><br>

    <button type="submit" name="payment_type" value="online">
    Pay Online
</button>

<br><br>

<button type="submit" name="payment_type" value="offline">
    Pay at Reception
</button>

</form>
