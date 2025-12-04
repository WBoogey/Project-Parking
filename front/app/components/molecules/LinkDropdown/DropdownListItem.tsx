import type { DropdownListElementType } from "@/types/LinkDropdownTypes";

interface DropdownListItemProps {
  item: DropdownListElementType;
}

const DropdownListItem = ({ item }: DropdownListItemProps) => {
  return (
    <li>
      <a
        href={item.href}
        className="whitespace-nowrap font-semibold text-sm text-secondary hover:text-black hover:underline"
      >
        {item.title}
      </a>
    </li>
  );
};

export default DropdownListItem;
