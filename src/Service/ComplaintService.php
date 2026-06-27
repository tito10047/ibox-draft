<?php

namespace App\Service;

use App\Enum\ComplaintType;
use App\Model\Complaint;
use Symfony\Component\HttpFoundation\RequestStack;

class ComplaintService
{
    /** @var Complaint[] */
    private array $data = [];

    public function __construct(
        private readonly RequestStack $requestStack
    )
    {
        $this->generateData();
    }

    private function generateData(): void
    {
        $subjects = [
            'Neighbor\'s dog is looking at me suspiciously',
            'Pigeons in the park are organizing gangs',
            'Too noisy grass after rain',
            'Seniors in public transport racing for seats',
            'Inappropriate behavior of a statue in the center',
            'The wind blows my hair into my eyes every time I go to the office',
            'The cat on the roof is stealing my Wi-Fi signal',
            'The park bench is too comfortable, people actually sit there',
            'The traffic light on the main street has a very rude orange',
            'My aunt claims the city radio is actually a podcast for squirrels',
            'City fountains spray water in a way that is too wet',
            'The pavement in the square reminds me of an unfinished Tetris',
            'City lighting shines too brightly, the stars feel ashamed',
            'Trash cans have too narrow necks, my piano didn\'t fit in there',
            'There are fish in the local pond that ignore me',
            'The bell tower strikes every hour as if it wants to prove something',
            'The city police have too polished cars, they blind me',
            'Market vendors sell apples that are too round',
            'Public toilets have too thin toilet paper, I feel vulnerable',
            'The city library has too many books, I can\'t choose',
            'Trains run too on time, I don\'t have time to finish my coffee',
            'Trees in the park drop leaves in autumn, it\'s a mess',
            'The city hall has too many doors, I always get lost',
            'The city radio announces things that don\'t interest me',
            'There is too much oxygen in the park, my head is spinning',
            'Local ice cream is too cold, my teeth hurt',
            'The postman only brings me bills, I demand love letters',
            'The city lawn is too green, it\'s an eyesore',
            'The sky over the city is blue, I prefer anthracite',
            'City pigeons refuse to pay parking fees',
            'Pedestrian crossings have too many stripes, it confuses me',
            'The city architect surely prefers concrete over me',
            'Blooming flowers on balconies smell too intensely',
            'Christmas decorations in July would be more original',
            'City buses have too large windows, people can see me'
        ];

        $descriptions = [
            'I request an immediate investigation into this animal\'s intentions. I feel like it knows something I don\'t.',
            'I noticed they exchange encrypted messages using cooing. I fear for the city\'s safety.',
            'That sound is unbearable. It sounds like the clapping of thousands of tiny hands. It\'s impossible to sleep.',
            'They use canes as weapons. I suggest installing seat belts on the seats.',
            'It stands there so provocatively. It\'s definitely judging me for my grocery shopping.',
            'It\'s a clear attack on my personal integrity. I request the construction of windbreaks along the entire route.',
            'Every time it sits on the roof ridge, my Netflix lags. It must be an agent of a telecommunications company.',
            'What is this world coming to, people just relaxing? Benches should be pointy so no one stays there.',
            'It flashes so fast I don\'t even have time to downshift. It\'s discrimination against owners of older cars.',
            'They broadcast hazelnut cake recipes there, but only in ultrasound. My aunt has an aluminum foil antenna for it.',
            'This water is wetter than the one at home in the shower. It\'s suspicious and probably chemically treated.',
            'Every day I try to fit in and it doesn\'t work. The city should rearrange the cubes.',
            'Stars have a right to privacy and darkness. The city should turn off the lights for at least half the night.',
            'I bought a new piano and wanted to throw it away. The city makes it impossible for me. I demand larger bins.',
            'I cast my line, but they just looked at me and swam away. I demand an apology.',
            'That bell sounds too confident. The city should quiet it down to make it more humble.',
            'When the sun shines, it reflects off their hood directly into my living room. It\'s bullying.',
            'Nature doesn\'t make perfect shapes. These apples are suspiciously symmetrical, they must be from a 3D printer.',
            'I\'m afraid it will break at the most inappropriate moment. I demand a two-ply standard.',
            'I read the titles of the first ten books and I was already tired. I suggest a reduction of the collection.',
            'When I arrive at the station, the train is already standing there. That is an unacceptable pace of life.',
            'I have to walk zigzag so I don\'t step on a yellow leaf. The city should vacuum the trees.',
            'I was looking for the mailroom and ended up in the boiler room. The city should only have one main door.',
            'Why do I care that there\'s a fair tomorrow? I want to hear the weather forecast for my bathroom.',
            'I feel too refreshed and it worries me. I suggest releasing carbon dioxide.',
            'I got a "brain freeze" after the first bite. The city should pre-heat the ice cream.',
            'My mailbox is full of negativity. I request the post office to filter out sad messages.',
            'That green is so vivid it makes my eyes burn. I suggest painting the lawn gray.',
            'Blue is too optimistic a color for this city. I request more clouds or a filter.',
            'I see them sitting on parked cars and not paying a cent. Where is the justice?',
            'Every time I cross, I try to jump over the black stripes and I look like a fool.',
            'Concrete, concrete, concrete everywhere. Not a single statue dedicated to my humble self.',
            'It attracted bees that are now following me all the way to the elevator. I demand a ban on blooming.',
            'At least we would have something to look forward to in this heat. I suggest Christmas trees with fans.',
            'I feel like I\'m in a reality show when I sit on the bus. I request opaque films.'
        ];

        $types = ComplaintType::cases();

        for ($i = 1; $i <= 215; $i++) {
            $subject = $subjects[$i % count($subjects)];
            $desc = $descriptions[$i % count($descriptions)];
            $type = $types[$i % count($types)];

            // Fixný dátum založený na ID pre konzistenciu
            $date = new \DateTimeImmutable("2024-01-01 12:00:00");
            $date = $date->modify("+$i days +$i hours");

            $this->data[$i] = new Complaint(
                $i,
                "$subject #$i",
                $date,
                "https://picsum.photos/seed/" . ($i + 100) . "/200/200",
                $desc,
                $type
            );
        }
    }

    public function get(int $id): ?Complaint
    {
        $session = $this->requestStack->getSession();
        if ($session->has('complaint'.$id)) {
            return $session->get('complaint'.$id);
        }
        return $this->data[$id] ?? null;
    }

    /**
     * @return Complaint[]
     */
    public function find(
        ?string $search = null,
        ?string $type = null,
        ?string $orderBy = 'id',
        string $order = 'ASC',
        int $perPage = 20,
        int $offset = 0
    ): array {
        $filtered = $this->data;

        if ($search) {
            $filtered = array_filter($filtered, function (Complaint $c) use ($search) {
                return stripos($c->title, $search) !== false || stripos($c->description, $search) !== false;
            });
        }

        if ($type && $type !== 'All') {
            $filtered = array_filter($filtered, function (Complaint $c) use ($type) {
                return $c->type->value === $type;
            });
        }

        // Sort
        usort($filtered, function (Complaint $a, Complaint $b) use ($orderBy, $order) {
            $valA = match ($orderBy) {
                'title' => $a->title,
                'date' => $a->date->getTimestamp(),
                'type' => $a->type->value,
                default => $a->id,
            };
            $valB = match ($orderBy) {
                'title' => $b->title,
                'date' => $b->date->getTimestamp(),
                'type' => $b->type->value,
                default => $b->id,
            };

            if ($valA == $valB) return 0;

            $res = ($valA < $valB) ? -1 : 1;
            return ($order === 'DESC') ? -$res : $res;
        });

        return array_slice($filtered, $offset, $perPage);
    }

    public function count(?string $search = null, ?string $type = null): int
    {
        // Pre zjednodušenie find bez limitu
        $filtered = $this->data;
        if ($search) {
            $filtered = array_filter($filtered, function (Complaint $c) use ($search) {
                return stripos($c->title, $search) !== false || stripos($c->description, $search) !== false;
            });
        }
        if ($type && $type !== 'All') {
            $filtered = array_filter($filtered, function (Complaint $c) use ($type) {
                return $c->type->value === $type;
            });
        }
        return count($filtered);
    }

    public function update(Complaint $complaint): void
    {
        $session = $this->requestStack->getSession();
        $session->set('complaint'.$complaint->id, $complaint);
    }
}
