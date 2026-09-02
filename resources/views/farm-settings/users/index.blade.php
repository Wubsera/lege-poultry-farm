<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $farm->farm_name ?? 'Farm Users' }}</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .header {
            background: #1f2937;
            color: white;
            padding: 20px 30px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0 0;
            color: #d1d5db;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .top-bar h2 {
            margin: 0;
        }

        .button {
            display: inline-block;
            padding: 11px 18px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .errors {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f9fafb;
            font-size: 14px;
        }

        td {
            font-size: 15px;
        }

        .admin-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1e40af;
            font-size: 12px;
            font-weight: bold;
        }

        .staff-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
            font-weight: bold;
        }

        .active-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: bold;
        }

        .inactive-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 12px;
            font-weight: bold;
        }

        .edit-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            margin-right: 12px;
        }

        .edit-link:hover {
            text-decoration: underline;
        }

        .status-form {
            display: inline;
        }

        .status-button {
            border: none;
            padding: 7px 11px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
        }

        .deactivate-button {
            background: #fee2e2;
            color: #991b1b;
        }

        .deactivate-button:hover {
            background: #fecaca;
        }

        .activate-button {
            background: #dcfce7;
            color: #166534;
        }

        .activate-button:hover {
            background: #bbf7d0;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        @media (max-width: 800px) {

            .container {
                margin: 20px auto;
                padding: 10px;
            }

            .card {
                padding: 20px;
            }

            .top-bar {
                align-items: flex-start;
                flex-direction: column;
            }

            th,
            td {
                padding: 10px 8px;
                font-size: 13px;
            }

            .edit-link {
                display: inline-block;
                margin-bottom: 8px;
            }

        }

    </style>

</head>

<body>

@include('layouts.farm-navigation')


<div class="header">

    <h1>
        👥 Farm Users
    </h1>

    <p>
        Manage users belonging to {{ $farm->farm_name ?? 'your farm' }}
    </p>

</div>


<div class="container">

    <div class="card">


        <div class="top-bar">

            <h2>
                Users
            </h2>

            <a
                href="{{ route('farm-settings.users.create') }}"
                class="button"
            >
                + Add User
            </a>

        </div>


        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        @if ($errors->any())

            <div class="errors">

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        @if ($users->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                Mobile Number
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($users as $user)

                            <tr>

                                <td>
                                    {{ $user->name }}
                                </td>

                                <td>
                                    {{ $user->mobile_number }}
                                </td>

                                <td>
                                    {{ $user->email ?? '—' }}
                                </td>

                                <td>

                                    @if ($user->is_admin)

                                        <span class="admin-badge">
                                            Farm Admin
                                        </span>

                                    @else

                                        <span class="staff-badge">
                                            Staff
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if ($user->is_admin)

                                        <span class="active-badge">
                                            Active
                                        </span>

                                    @elseif ($user->is_active)

                                        <span class="active-badge">
                                            Active
                                        </span>

                                    @else

                                        <span class="inactive-badge">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if (!$user->is_admin)

                                        <a
                                            href="{{ route(
                                                'farm-settings.users.edit',
                                                $user->id
                                            ) }}"
                                            class="edit-link"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route(
                                                'farm-settings.users.toggle-status',
                                                $user->id
                                            ) }}"
                                            method="POST"
                                            class="status-form"
                                            onsubmit="return confirm(
                                                '{{ $user->is_active
                                                    ? 'Are you sure you want to deactivate this user?'
                                                    : 'Are you sure you want to activate this user?' }}'
                                            );"
                                        >

                                            @csrf

                                            @method('PUT')

                                            @if ($user->is_active)

                                                <button
                                                    type="submit"
                                                    class="status-button deactivate-button"
                                                >
                                                    Deactivate
                                                </button>

                                            @else

                                                <button
                                                    type="submit"
                                                    class="status-button activate-button"
                                                >
                                                    Activate
                                                </button>

                                            @endif

                                        </form>

                                    @else

                                        <span>
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">

                No users have been added to this farm yet.

            </div>

        @endif


        <a
            href="/farm-settings"
            class="back"
        >
            ← Back to Farm Settings
        </a>


    </div>

</div>


</body>

</html>
