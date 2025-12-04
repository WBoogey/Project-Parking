import type {
  DropdownListElementType,
  DropdownListType,
} from "@/types/LinkDropdownTypes";
import DropdownListItem from "./DropdownListItem";

interface DropdownListProps {
  list: DropdownListType;
}

const DropdownList = ({ list }: DropdownListProps) => {
  return (
    <ul className="flex flex-col gap-2">
      {list.map((item: DropdownListElementType, index: number) => (
        <DropdownListItem key={index} item={item} />
      ))}
    </ul>
  );
};

export default DropdownList;
