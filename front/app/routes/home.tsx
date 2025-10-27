import type { Route } from "./+types/home";

export function meta({}: Route.MetaArgs) {
  return [
    { title: "Project Parking" },
    { name: "description", content: "Project Parking" },
  ];
}

export default function Home() {
  return <div>Home</div>;
}
