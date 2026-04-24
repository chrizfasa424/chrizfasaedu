<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schools = DB::table('schools')
            ->select('id', 'name', 'code')
            ->get()
            ->keyBy('id');

        $students = DB::table('students')
            ->select('id', 'school_id', 'admission_id', 'admission_number', 'registration_number', 'created_at')
            ->orderBy('school_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        $grouped = [];
        foreach ($students as $student) {
            $year = $this->resolveYear(
                (string) ($student->admission_number ?? ''),
                (string) ($student->registration_number ?? ''),
                (string) ($student->created_at ?? '')
            );

            $key = ((int) $student->school_id) . '|' . $year;
            $grouped[$key][] = $student;
        }

        $assignments = [];
        foreach ($grouped as $key => $rows) {
            [$schoolId, $year] = explode('|', $key, 2);
            $schoolId = (int) $schoolId;
            $prefix = $this->resolveSchoolPrefix(
                (string) ($schools[$schoolId]->code ?? ''),
                (string) ($schools[$schoolId]->name ?? '')
            );

            $sequence = 1;
            foreach ($rows as $student) {
                $number = sprintf('%s/%s/%04d', $prefix, $year, $sequence);
                $assignments[] = [
                    'student_id' => (int) $student->id,
                    'admission_id' => $student->admission_id ? (int) $student->admission_id : null,
                    'number' => $number,
                ];
                $sequence++;
            }
        }

        DB::transaction(function () use ($assignments): void {
            $now = now();

            foreach ($assignments as $entry) {
                $temporary = '__TMP__' . $entry['student_id'];
                DB::table('students')
                    ->where('id', $entry['student_id'])
                    ->update([
                        'admission_number' => $temporary,
                        'registration_number' => $temporary,
                        'updated_at' => $now,
                    ]);
            }

            foreach ($assignments as $entry) {
                DB::table('students')
                    ->where('id', $entry['student_id'])
                    ->update([
                        'admission_number' => $entry['number'],
                        'registration_number' => $entry['number'],
                        'updated_at' => $now,
                    ]);

                if (!empty($entry['admission_id'])) {
                    DB::table('admissions')
                        ->where('id', (int) $entry['admission_id'])
                        ->update([
                            'admission_number' => $entry['number'],
                            'updated_at' => $now,
                        ]);
                }
            }
        });
    }

    public function down(): void
    {
        // No rollback: this migration normalizes historical student registration numbering.
    }

    private function resolveYear(string $admissionNumber, string $registrationNumber, string $createdAt): string
    {
        foreach ([$admissionNumber, $registrationNumber] as $value) {
            if (preg_match('/^[A-Z0-9]+\/(\d{4})\/\d{4}$/i', strtoupper(trim($value)), $matches) === 1) {
                return $matches[1];
            }
        }

        $timestamp = strtotime($createdAt);
        if ($timestamp !== false) {
            return date('Y', $timestamp);
        }

        return date('Y');
    }

    private function resolveSchoolPrefix(string $code, string $name): string
    {
        $normalizedCode = strtoupper(trim($code));
        if ($normalizedCode !== '' && preg_match('/^[A-Z]{2,6}$/', $normalizedCode) === 1) {
            return $normalizedCode;
        }

        $prefix = $this->initialsFromName($name);
        if ($prefix !== '') {
            return $prefix;
        }

        return 'EIS';
    }

    private function initialsFromName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }

        $words = preg_split('/\s+/', $name) ?: [];
        $initials = '';
        foreach ($words as $word) {
            $clean = preg_replace('/[^a-z]/i', '', (string) $word) ?? '';
            if ($clean === '' || in_array(strtolower($clean), ['and', 'of', 'the'], true)) {
                continue;
            }

            $initials .= strtoupper($clean[0]);
            if (strlen($initials) >= 6) {
                break;
            }
        }

        if ($initials !== '') {
            return $initials;
        }

        $letters = strtoupper(preg_replace('/[^a-z]/i', '', $name) ?? '');
        return substr($letters, 0, 6);
    }
};
