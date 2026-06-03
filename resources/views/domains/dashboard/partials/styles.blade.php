    <style>
        .custom-grid-4 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .custom-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1280px) {
            .custom-grid-4 {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .custom-grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        @media (min-width: 1024px) {
            .custom-grid-2 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
