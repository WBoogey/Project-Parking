import type { DropdownFullListType } from "@/types/LinkDropdownTypes";

type NavbarDropdownData = {
  title: string;
  elements: DropdownFullListType;
};

export const searchDropdownData: NavbarDropdownData = {
  title: "Vous cherchez une place",
  elements: [
    [
      { title: "primary text", href: "#" },
      { title: "primary text", href: "#" },
    ],
    [{ title: "primary text", href: "#" }],
  ],
};

export const ownerDropdownData: NavbarDropdownData = {
  title: "Vous êtes propriétaire",
  elements: [
    [
      { title: "primary text", href: "#" },
      { title: "primary text", href: "#" },
    ],
    [{ title: "primary text", href: "#" }],
  ],
};
